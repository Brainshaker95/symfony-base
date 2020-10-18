<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\User;
use App\Form\Type\GalleryType;
use App\Traits\HasAssetService;
use App\Traits\HasEntityManager;
use App\Traits\HasFileService;
use App\Traits\HasHashService;
use App\Traits\HasImageRepository;
use App\Traits\HasTranslator;
use App\Traits\HasUploadDirectory;
use App\Traits\HasUserService;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints;

class GalleryController extends FrontendController
{
    use HasAssetService;
    use HasEntityManager;
    use HasFileService;
    use HasHashService;
    use HasImageRepository;
    use HasTranslator;
    use HasUserService;
    use HasUploadDirectory;

    private const PAGE_SIZE = 20;

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
            return $this->renderNotFound();
        }

        $form->handleRequest($request);

        if ($request->isMethod('POST') && $form->isSubmitted()) {
            $hasErrors = !$this->handleForm($form, $user);

            if (!$hasErrors) {
                $page = 1;
            }
        }

        $paginator  = $this->imageRepository->getGalleryPaginator($page, $limit);
        $totalPages = (int) ceil($paginator->count() / $limit);

        $this->userService->clearUserFilesFromTmp($user);

        if ($page > $totalPages) {
            return $this->renderNotFound();
        }

        return $this->render('page/gallery.html.twig', [
            'gallery_form' => $form->createView(),
            'has_errors'   => $hasErrors,
            'paginator'    => $paginator,
            'current_page' => $page,
            'total_pages'  => $totalPages,
        ]);
    }

    /**
     * @return Response|JsonResponse
     */
    public function pagingAction(Request $request)
    {
        /**
         * @var User|null
         */
        $user   = $this->getUser();
        $images = [];

        if ($user) {
            $limit = self::PAGE_SIZE;
            $page  = (int) $request->get('page', 1);

            if ($page < 1) {
                return $this->notFound();
            }

            $paginator  = $this->imageRepository->getGalleryPaginator($page, $limit);
            $totalPages = (int) ceil($paginator->count() / $limit);

            if ($page > $totalPages) {
                return $this->notFound();
            }

            foreach ($paginator as $image) {
                $path = $image->getPath();

                $images[] = [
                    'link__href'      => $path,
                    'image__src'      => $this->assetService->getPreviewSrc($path, 'gallery_image'),
                    'image__data-src' => $this->assetService->getThumbnailSrc($path, 'gallery_image'),
                ];
            }
        }

        return $this->json([
            'success' => count($images) > 0,
            'images'  => $images,
        ]);
    }

    public function uploadImagesAction(Request $request): Response
    {
        /**
         * @var User|null
         */
        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                'success'  => false,
                'filename' => null,
            ]);
        }

        $encodedUserId = $this->hashService->encode($user->getId());
        $success       = false;
        $filename      = null;

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
        $successCount   = 0;

        $form->clearErrors(true);

        if (is_dir($tmpFolder)) {
            $files = $finder->files()->in($tmpFolder);
        }

        foreach ($constraints as $constraint) {
            if ($constraint instanceof Constraints\All) {
                $fileConstraint = $constraint->constraints[0];
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
                    $successCount += 1;
                } else {
                    $this->addFlash('error', 'page.gallery.image_save.error');
                }

                $this->fileService->delete($filename, 'tmp');
            }
        }

        if ($successCount > 1) {
            $this->addFlash('success', 'page.gallery.image_save.success.multiple');
            $this->addFlash('_params', serialize([
                '{{ successCount }}' => $successCount,
            ]));
        } elseif ($successCount > 0) {
            $this->addFlash('success', 'page.gallery.image_save.success.single');
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
