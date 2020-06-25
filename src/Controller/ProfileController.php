<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\User;
use App\Form\Type\UserType;
use App\Service\FileService;
use App\Service\HashService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends FrontendController
{
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

    public function __construct(
        string $uploadDirectory,
        EntityManagerInterface $entityManager,
        FileService $fileService,
        HashService $hashService
    ) {
        $this->uploadDirectory = $uploadDirectory;
        $this->entityManager   = $entityManager;
        $this->fileService     = $fileService;
        $this->hashService     = $hashService;
    }

    public function profileAction(Request $request): Response
    {
        /**
         * @var User|null
         */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(UserType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var UploadedFile
             */
            $uploadedImage = $form->get('image')->getData();
            $filename      = $this->fileService->upload($uploadedImage, 'profile_images');

            if ($filename) {
                $this->updateProfileImage($user, $filename);
                $this->addFlash('success', 'page.profile.image_upload.success');
            } else {
                $this->addFlash('error', 'page.profile.image_upload.error');
            }
        }

        return $this->render('page/profile.html.twig', [
            'user'         => $user,
            'profile_form' => $form->createView(),
        ]);
    }

    private function updateProfileImage(User $user, string $filename): void
    {
        $image    = new Image();
        $oldImage = $user->getImage();

        $image
            ->setFilename($filename)
            ->setPath('/uploads/profile_images/' . $filename)
            ->setType('profile');

        if ($oldImage) {
            $finder = new Finder();
            $files  = $finder->files()->in(__DIR__ . '..\\..\\..\\public\\uploads');

            foreach ($files as $file) {
                $path = $oldImage->getPath() ?: '';

                if (strpos(str_replace('/', '\\', $path), $file->getRelativePathname())) {
                    unlink($this->uploadDirectory . '/..' . $path);
                }
            }

            $this->entityManager->remove($oldImage);
        }

        $user->setImage($image);

        $this->entityManager->persist($image);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
