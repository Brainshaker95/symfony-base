<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\User;
use App\Form\Type\GalleryType;
use App\Repository\ImageRepository;
use App\Service\FileService;
use App\Service\HashService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints;
use Symfony\Contracts\Translation\TranslatorInterface;

class GalleryController extends FrontendController
{
    protected const PAGE_SIZE         = 5;
    protected const MAX_VISIBLE_PAGES = 5;

    /**
     * @var string
     */
    protected $uploadDirectory;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var FileService
     */
    protected $fileService;

    /**
     * @var HashService
     */
    protected $hashService;

    /**
     * @var ImageRepository
     */
    protected $imageRepository;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var UserService
     */
    protected $userService;

    public function __construct(
        string $uploadDirectory,
        EntityManagerInterface $entityManager,
        FileService $fileService,
        HashService $hashService,
        ImageRepository $imageRepository,
        TranslatorInterface $translator,
        UserService $userService
    ) {
        $this->uploadDirectory = $uploadDirectory;
        $this->entityManager   = $entityManager;
        $this->fileService     = $fileService;
        $this->hashService     = $hashService;
        $this->imageRepository = $imageRepository;
        $this->translator      = $translator;
        $this->userService     = $userService;
    }

    public function galleryAction(Request $request): Response
    {
        /**
         * @var User|null
         */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        /**
         * @var Form
         */
        $form      = $this->createForm(GalleryType::class);
        $hasErrors = false;
        $limit     = self::PAGE_SIZE;
        $page      = (int) $request->get('page', 1);

        if ($page < 1) {
            $page = 1;
        }

        $paginator  = $this->imageRepository->getGalleryPaginator($page, $limit);
        $totalPages = ceil($paginator->count() / $limit);

        if ($page > $totalPages) {
            $page      = (int) $totalPages;
            $paginator = $this->imageRepository->getGalleryPaginator($page, $limit);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $hasErrors = !$this->handleForm($form, $user);
        }

        $this->userService->clearUserFilesFromTmp($user);

        return $this->render('page/gallery.html.twig', [
            'gallery_form'      => $form->createView(),
            'has_errors'        => $hasErrors,
            'paginator'         => $paginator,
            'current_page'      => $page,
            'page_size'         => $limit,
            'total_pages'       => $totalPages,
            'max_visible_pages' => self::MAX_VISIBLE_PAGES,
        ]);
    }

    public function uploadImagesAction(Request $request): Response
    {
        /**
         * @var User|null
         */
        $user     = $this->getUser();
        $success  = false;
        $filename = null;

        if ($user) {
            $encodedUserId = $this->hashService->encode($user->getId());

            if ($request->isMethod('DELETE')) {
                $filename = $request->get('filename', '');

                if (strpos($filename, $encodedUserId) === 0
                    && $this->fileService->delete($filename, 'tmp')) {
                    $success = true;
                }
            } else {
                foreach ($request->files as $file) {
                    $filename = $this->fileService->upload($file, 'tmp', $encodedUserId . '-');

                    if ($filename) {
                        $success = true;
                    } else {
                        $success = false;
                    }
                }
            }
        }

        return $this->json([
            'success'  => $success,
            'filename' => $filename,
        ]);
    }

    private function handleForm(Form $form, User $user): bool
    {
        $encodedUserId  = $this->hashService->encode($user->getId());
        $finder         = new Finder();
        $tmpFolder      = __DIR__ . '..\\..\\..\\public\\uploads\\tmp';
        $constraints    = $form->get('image')->getConfig()->getOption('constraints');
        $fileConstraint = null;
        $files          = [];

        $form->clearErrors(true);

        if (is_dir($tmpFolder)) {
            $files = $finder->files()->in($tmpFolder);
        }

        foreach ($constraints as $constraint) {
            if ($constraint instanceof Constraints\File) {
                $fileConstraint = $constraint;
            }
        }

        foreach ($files as $file) {
            $filename = $file->getRelativePathname();

            if (strpos($filename, $encodedUserId) === 0) {
                $form = $this->validateFile($filename, $file, $form, $fileConstraint);

                if ($form->getErrors(true)->count()) {
                    $this->userService->clearUserFilesFromTmp($user);

                    return false;
                }

                $newFilename = substr($filename, strlen($encodedUserId) + 1);

                if ($this->fileService->move($filename, 'tmp', $newFilename, 'gallery_images')) {
                    $this->addImageToGallery($user, $newFilename);
                    $this->addFlash('success', 'page.gallery.image_save.success');
                } else {
                    $this->addFlash('error', 'page.gallery.image_save.error');
                }

                $this->fileService->delete($filename, 'tmp');
            }
        }

        return true;
    }

    /**
     * @param Constraints\File|null $fileConstraint
     */
    private function validateFile(string $filename, SplFileInfo $file, Form $form, $fileConstraint): Form
    {
        if (!$fileConstraint) {
            return $form;
        }

        $image = $form->get('image');

        if ($file->getSize() > $fileConstraint->maxSize) {
            $image->addError(new FormError(
                $this->translator->trans($fileConstraint->maxSizeMessage, [
                    '{{ limit }}' => $fileConstraint->maxSize / 1000000,
                ], 'validators')
            ));
        }

        $mimeType = mime_content_type($this->uploadDirectory . '/tmp/' . $filename);

        if (!in_array($mimeType, $fileConstraint->mimeTypes)) {
            $image->addError(new FormError(
                $this->translator->trans($fileConstraint->mimeTypesMessage, [
                    '{{ types }}' => '"' . implode('", "', $fileConstraint->mimeTypes) . '"',
                ], 'validators')
            ));
        }

        return $form;
    }

    private function addImageToGallery(User $user, string $filename): void
    {
        $image = new Image();

        $image
            ->setFilename($filename)
            ->setPath('/uploads/gallery_images/' . $filename)
            ->setType('gallery');

        $this->entityManager->persist($image);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
