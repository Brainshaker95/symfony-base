<?php

namespace App\Controller;

use App\Entity\Asset\AbstractAsset;
use App\Entity\Asset\Asset;
use App\Entity\Asset\Image;
use App\Entity\Asset\Video;
use App\Entity\User;
use App\Form\Type\GalleryType;
use App\Traits\HasAssetRepository;
use App\Traits\HasAssetService;
use App\Traits\HasEntityManager;
use App\Traits\HasFileService;
use App\Traits\HasHashService;
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
    use HasAssetRepository;
    use HasAssetService;
    use HasEntityManager;
    use HasFileService;
    use HasHashService;
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

        $page = (int) $request->get('page', 1);

        if ($page < 1) {
            return $this->renderNotFound();
        }

        /**
         * @var Form
         */
        $form      = $this->createForm(GalleryType::class);
        $hasErrors = false;
        $limit     = self::PAGE_SIZE;

        $form->handleRequest($request);

        if ($request->isMethod('POST') && $form->isSubmitted()) {
            $hasErrors = !$this->handleForm($form, $user);

            if (!$hasErrors) {
                $page = 1;
            }
        }

        $paginator  = $this->assetRepository->getGalleryPaginator($page, $limit);
        $totalPages = (int) ceil($paginator->count() / $limit);

        $this->userService->clearUserFilesFromTmp($user);

        if ($page > $totalPages && $page !== 1) {
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
        $assets = [];

        if ($user) {
            $limit = self::PAGE_SIZE;
            $page  = (int) $request->get('page', 1);

            if ($page < 1) {
                return $this->notFound();
            }

            $paginator  = $this->assetRepository->getGalleryPaginator($page, $limit);
            $totalPages = (int) ceil($paginator->count() / $limit);

            if ($page > $totalPages) {
                return $this->notFound();
            }

            foreach ($paginator as $assetObj) {
                $asset = $assetObj->getAsset();

                if (!$asset) {
                    continue;
                }

                $path      = $asset->getPath();
                $assetData = [];

                if ($assetObj->getType() === 'image') {
                    $assetData = [
                        'image__src'      => $this->assetService->getPreviewSrc($path, 'gallery_image'),
                        'image__data-src' => $this->assetService->getThumbnailSrc($path, 'gallery_image'),
                        'is-image'        => true,
                    ];
                } else {
                    $assetData = [
                        'video__src' => $path,
                        'is-video'   => true,
                    ];
                }

                $assets[] = array_merge([
                    'link__href' => $path,
                ], $assetData);
            }
        }

        return $this->json([
            'success' => count($assets) > 0,
            'assets'  => $assets,
        ]);
    }

    public function uploadAssetsAction(Request $request): Response
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
        $constraints    = $form->get('assets')->getConfig()->getOption('constraints');
        $fileConstraint = null;
        $files          = [];
        $successCount   = 0;

        if (!$form->get('privacyAndTerms')->getData()) {
            return false;
        }

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
                    return false;
                }

                $newFilename = substr($filename, strlen($encodedUserId) + 1);

                if ($this->fileService->move($filename, 'tmp', $newFilename, 'gallery-assets')) {
                    $this->addAssetToGallery($user, $newFilename);
                    $successCount += 1;
                } else {
                    $this->addFlash('error', 'page.gallery.asset_save.error');
                }

                $this->fileService->delete($filename, 'tmp');
            }
        }

        if ($successCount > 1) {
            $this->addFlash('success', 'page.gallery.asset_save.success.multiple');
            $this->addFlash('_params', serialize([
                '{{ successCount }}' => $successCount,
            ]));
        } elseif ($successCount > 0) {
            $this->addFlash('success', 'page.gallery.asset_save.success.single');
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

        $assets = $form->get('assets');

        if ($file->getSize() > $fileConstraint->maxSize) {
            $assets->addError(new FormError(
                $this->translator->trans($fileConstraint->maxSizeMessage, [
                    '{{ limit }}' => $fileConstraint->maxSize / 1000000,
                ], 'validators')
            ));
        }

        $mimeType = mime_content_type($this->uploadDirectory . '/tmp/' . $filename);

        if (!in_array($mimeType, $fileConstraint->mimeTypes)) {
            $assets->addError(new FormError(
                $this->translator->trans($fileConstraint->mimeTypesMessage, [
                    '{{ types }}' => '"' . implode('", "', $fileConstraint->mimeTypes) . '"',
                ], 'validators')
            ));
        }

        return $form;
    }

    private function addAssetToGallery(User $user, string $filename): void
    {
        $mimeType = mime_content_type($this->uploadDirectory . '/gallery-assets/' . $filename);

        if ($mimeType === 'video/mp4') {
            $asset = $this->createAsset(Video::class, 'video');
        } else {
            $asset = $this->createAsset(Image::class, 'image');
        }

        $asset
            ->setFilename($filename)
            ->setPath('/uploads/gallery-assets/' . $filename)
            ->setType('gallery')
            ->setUser($user);

        $this->entityManager->persist($asset);
        $this->entityManager->flush();
    }

    private function createAsset(string $class, string $type): AbstractAsset
    {
        $asset    = new $class();
        $assetObj = new Asset();

        $assetObj
            ->setType($type)
            ->setAsset($asset);

        $this->entityManager->persist($assetObj);

        return $asset;
    }
}
