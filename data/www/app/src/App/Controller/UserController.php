<?php

namespace App\Controller;

use App\Auth\Authenticator;
use App\Auth\UserManager;
use App\Entity\User;
use App\Form\UserDeleteForm;
use App\Form\UserEditForm;
use App\Form\UserRegisterForm;
use Beaver\Controller\AbstractController;
use Beaver\Request\Request;
use Beaver\Response\Response;

class UserController extends AbstractController
{
    public function register()
    {
        /** @var UserManager $userManager */
        $userManager = $this->get('user.manager');

        /** @var UserRegisterForm $formValidator */
        $formValidator = $this->get('user.register.form');

        if (Request::POST === $this->request->getHttpMethod()) {
            if ($formData = $formValidator->validate($this->request)) {
                // Error during insertion
                if (!$userManager->register($formData)) {
                    $this->addNotification('Un problème est survenu durant l\'enregistrement ! Veuillez vérifier que les mots de passe sont identiques !', 'danger');

                    return $this->render('user/register.html.twig');
                }
                $this->addNotification('Votre compte à bien été créé, pensez à vous connecter !', 'success');

                return $this->redirect($this->get('router')->generatePath('index'));
            } else {
                $this->addNotification('Certains champs n\'ont pas le format attendu', 'danger');

                return $this->render('user/register.html.twig');
            }
        }

        return $this->render('user/register.html.twig');
    }

    public function edit()
    {
        /** @var Authenticator $authenticator */
        $authenticator = $this->get('authenticator');
        /** @var User $user */
        $user = $authenticator->getUser();

        if (!$user) {
            $this->addNotification('Vous n\'etes pas connecté, la page est inaccessible.', 'danger');
            return $this->redirect($this->get('router')->generatePath('index'));
        }

        /** @var UserManager $userManager */
        $userManager = $this->get('user.manager');

        /** @var UserEditForm $formValidator */
        $formValidator = $this->get('user.edit.form');

        if (Request::POST === $this->request->getHttpMethod()) {
            if ($formData = $formValidator->validate($this->request)) {
                $formData['email'] = $user->getEmail();
                // Error during updating
                if (!$userManager->update($formData)) {
                    $this->addNotification('Un problème est survenu durant l\'enregistrement !', 'danger');
                    return $this->render('user/edit.html.twig');
                }

                $this->addNotification('Votre compte à bien été modifié !', 'success');
                return $this->redirect($this->get('router')->generatePath('user.edit'));
            }
            else {
                $this->addNotification('Certains champs n\'ont pas le format attendu', 'danger');
                return $this->render('user/edit.html.twig');
            }
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
        ]);
    }

    public function editPassword()
    {
        /** @var Authenticator $authenticator */
        $authenticator = $this->get('authenticator');
        /** @var User $user */
        $user = $authenticator->getUser();

        if (!$user) {
            $this->addNotification('Vous n\'etes pas connecté, la page est inaccessible.', 'danger');
            return $this->redirect($this->get('router')->generatePath('index'));
        }

        /** @var UserManager $userManager */
        $userManager = $this->get('user.manager');

        /** @var UserEditForm $formValidator */
        $formValidator = $this->get('user.editPassword.form');

        if (Request::POST === $this->request->getHttpMethod()) {
            if ($formData = $formValidator->validate($this->request)) {
                $formData['email'] = $user->getEmail();
                // Error during updating
                if (!$userManager->updatePassword($formData)) {
                    $this->addNotification('Un problème est survenu durant l\'enregistrement !', 'danger');
                    return $this->render('user/edit.html.twig');
                }

                $this->addNotification('Votre mot de passe à été modifié !', 'success');
                return $this->redirect($this->get('router')->generatePath('user.edit'));
            }
            else {
                $this->addNotification('Certains champs n\'ont pas le format attendu', 'danger');
                return $this->render('user/edit.html.twig');
            }
        }

        return $this->render('user/editPassword.html.twig');
    }

    public function delete()
    {
        /** @var Authenticator $authenticator */
        $authenticator = $this->get('authenticator');
        /** @var User $user */
        $user = $authenticator->getUser();

        if (!$user) {
            $this->addNotification('Vous n\'etes pas connecté, la page est inaccessible.', 'danger');

            return $this->redirect($this->get('router')->generatePath('index'));
        }

        /** @var UserManager $userManager */
        $userManager = $this->get('user.manager');

        if (Request::POST === $this->request->getHttpMethod()) {
            if (!$userManager->delete(['email' => $user->getEmail()])) {
                $this->addNotification('Une erreur est survenue durant la suppression, votre compte n\'est pas supprimé.', 'danger');

                return $this->redirect($this->get('router')->generatePath('user.edit'));
            }

            $authenticator->disconnect();

            return $this->redirect($this->get('router')->generatePath('index'));
        }

        return $this->redirect($this->get('router')->generatePath('user.edit'));
    }
}
