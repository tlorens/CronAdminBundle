<?php

namespace tlorens\CronAdminBundle\Controller;

use Cron\CronBundle\Entity\CronJob;
use Cron\CronBundle\Cron\Manager as CronManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use tlorens\CronAdminBundle\Form\CronJobType;

class CronAdminController extends AbstractController
{
   /** @var CronManager $cronManager Cron manager */
    private $cronManager;



    public function __construct(CronManager $cronManager)
    {
        $this->cronManager = $cronManager;
    }

    /**
     * Cron Job Listing
     *
     * @return Response
     */
    public function index() : Response
    {
        $cronJobs = $this->cronManager->listJobs();
        return $this->render('@CronAdmin/cronjob/index.html.twig', [ 'cronjobs' => $cronJobs ]);
    }

    /**
     * Add a new Cron Job
     *
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request) : Response
    {
        $cronJob = new CronJob();
        $form = $this->createForm(CronJobType::class, $cronJob);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cronJob = $form->getData();
            $this->cronManager->saveJob($cronJob);
            $this->addFlash('success', 'Cron job saved.');
            return $this->redirectToRoute('cron_admin_cron_list');
        }

        return $this->render('@CronAdmin/cronjob/new.html.twig', [
            'cronJob' => $cronJob,
            'form'    => $form->createView(),
        ]);
    }

    /**
     * Edit a Cron Job
     *
     * @param Request $request
     * @param Cronjob $cronJob
     *
     * @return Response
     */
    public function edit(Request $request, Cronjob $cronJob) : Response
    {
        $form = $this->createForm(CronJobType::class, $cronJob);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->cronManager->saveJob($cronJob);
            $this->addFlash('success', 'Cron job  updated.');
            return $this->redirectToRoute('cron_admin_cron_list');
        }

        return $this->render('@CronAdmin/cronjob/edit.html.twig', [
            'cronJob' => $cronJob,
            'form'    => $form->createView(),
        ]);
    }

    /**
     * Delete a Cron Job
     *
     * @param Request $request
     * @param CronJob $cronJob
     *
     * @return Response
     */
    public function delete(Request $request, CronJob $cronJob) : Response
    {
        if ($this->isCsrfTokenValid('delete'.$cronJob->getId(), $request->request->get('_token'))) {
            $this->cronManager->deleteJob($cronJob);
            $this->addFlash('success', 'Cron job deleted.');
        }

        return $this->redirectToRoute('cron_admin_cron_list');
    }
}
