<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

class UnicardController extends AbstractController
{
    #[Route('/unicard', name: 'unicard_page', methods: ['GET'])]
    public function unicardPage(): Response
    {
        return $this->render('unicard.html.twig');
    }

    #[Route('/unicard/add-bonus', name: 'unicard_add_bonus', methods: ['POST'])]
    public function addBonus(KernelInterface $kernel, Environment $twig): Response
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $input = new ArrayInput([
            'command' => 'addBonusCommand',
        ]);

        $output = new BufferedOutput();
        $application->run($input, $output);

        $content = $output->fetch();

        return new Response($twig->render('unicard.html.twig', [
            'output' => $content,
        ]));
    }
}
