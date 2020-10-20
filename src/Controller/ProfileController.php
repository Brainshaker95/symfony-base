<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\User;
use App\Form\Type\UserType;
use App\Traits\HasEntityManager;
use App\Traits\HasFileService;
use App\Traits\HasHashService;
use App\Traits\HasUploadDirectory;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends FrontendController
{
    use HasEntityManager;
    use HasFileService;
    use HasHashService;
    use HasUploadDirectory;

    public function profileAction(Request $request): Response
    {
        /**
         * @var User|null
         */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var UploadedFile|null
             */
            $uploadedImage = $form->get('image')->getData();
            $hasErrors     = false;

            if ($uploadedImage) {
                $filename = $this->fileService->upload($uploadedImage, 'profile_images');

                if ($filename) {
                    $this->updateProfileImage($user, $filename);
                } else {
                    $hasErrors = true;
                }
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            if ($hasErrors) {
                $this->addFlash('error', 'page.profile.update.error');
            } else {
                $this->addFlash('success', 'page.profile.update.success');
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
    }
}
