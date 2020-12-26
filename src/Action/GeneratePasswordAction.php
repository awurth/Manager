<?php

namespace App\Action;

use App\Action\Traits\TwigTrait;
use App\Form\Model\GeneratePassword;
use App\Form\Type\Action\GeneratePasswordType;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/password", name="app_generate_password")
 */
final class GeneratePasswordAction
{
    use TwigTrait;

    private FormFactoryInterface $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @psalm-suppress MissingPropertyType
     */
    public function __invoke(Request $request): Response
    {
        $model = new GeneratePassword();
        $form = $this->formFactory->create(GeneratePasswordType::class, $model);
        $form->handleRequest($request);

        $password = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $generator = (new ComputerPasswordGenerator())
                ->setLowercase($model->lowercase)
                ->setUppercase($model->uppercase)
                ->setNumbers($model->numbers)
                ->setSymbols($model->symbols)
                ->setLength($model->length);

            $password = $generator->generatePassword();
        }

        return $this->renderPage('generate-password', 'app/generate_password.html.twig', [
            'form' => $form->createView(),
            'password' => $password
        ]);
    }
}
