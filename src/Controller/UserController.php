<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Siganushka\UserBundle\Form\UserType;
use Siganushka\UserBundle\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    public function __construct(private readonly UserRepository $repository)
    {
    }

    public function getCollection(PaginatorInterface $paginator): Response
    {
        $queryBuilder = $this->repository->createQueryBuilderWithOrderBy('u');
        $pagination = $paginator->paginate($queryBuilder);

        return $this->json($pagination, context: [
            'groups' => ['user:collection'],
        ]);
    }

    public function postCollection(Request $request, EntityManagerInterface $entityManager): Response
    {
        $entity = $this->repository->createNew();

        $form = $this->createForm(UserType::class, $entity);
        $form->submit($request->getPayload()->all());

        if (!$form->isValid()) {
            return $this->json($form, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($entity);
        $entityManager->flush();

        return $this->json($entity, Response::HTTP_CREATED, context: [
            'groups' => ['user:item'],
        ]);
    }

    public function getItem(int $id): Response
    {
        $entity = $this->repository->find($id)
            ?? throw $this->createNotFoundException();

        return $this->json($entity, context: [
            'groups' => ['user:item'],
        ]);
    }

    public function putItem(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $entity = $this->repository->find($id)
            ?? throw $this->createNotFoundException();

        $form = $this->createForm(UserType::class, $entity);
        $form->submit($request->getPayload()->all(), !$request->isMethod('PATCH'));

        if (!$form->isValid()) {
            return $this->json($form, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->flush();

        return $this->json($entity, context: [
            'groups' => ['user:item'],
        ]);
    }

    public function deleteItem(EntityManagerInterface $entityManager, int $id): Response
    {
        $entity = $this->repository->find($id)
            ?? throw $this->createNotFoundException();

        $entityManager->remove($entity);
        $entityManager->flush();

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
