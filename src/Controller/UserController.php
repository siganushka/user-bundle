<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Siganushka\GenericBundle\Dto\PaginationDto;
use Siganushka\UserBundle\Form\UserType;
use Siganushka\UserBundle\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    public function __construct(private readonly UserRepository $repository)
    {
    }

    #[Route('/users', methods: 'GET')]
    public function getCollection(PaginatorInterface $paginator, #[MapQueryString] PaginationDto $dto): Response
    {
        $queryBuilder = $this->repository->createQueryBuilderWithOrderBy('u');
        $pagination = $paginator->paginate($queryBuilder, $dto->page, $dto->size);

        return $this->json($pagination, context: [
            'groups' => ['collection'],
        ]);
    }

    #[Route('/users', methods: 'POST')]
    public function postCollection(Request $request, EntityManagerInterface $entityManager): Response
    {
        $entity = $this->repository->createNew();

        $form = $this->createForm(UserType::class, $entity);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->json($form, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($entity);
        $entityManager->flush();

        return $this->json($entity, Response::HTTP_CREATED, context: [
            'groups' => ['item'],
        ]);
    }

    #[Route('/users/{id<\d+>}', methods: 'GET')]
    public function getItem(int $id): Response
    {
        $entity = $this->repository->find($id)
            ?? throw $this->createNotFoundException();

        return $this->json($entity, context: [
            'groups' => ['item'],
        ]);
    }

    #[Route('/users/{id<\d+>}', methods: ['PUT', 'PATCH'])]
    public function putItem(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $entity = $this->repository->find($id)
            ?? throw $this->createNotFoundException();

        $form = $this->createForm(UserType::class, $entity);
        $form->submit($request->request->all(), !$request->isMethod('PATCH'));

        if (!$form->isValid()) {
            return $this->json($form, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->flush();

        return $this->json($entity, context: [
            'groups' => ['item'],
        ]);
    }

    #[Route('/users/{id<\d+>}', methods: 'DELETE')]
    public function deleteItem(EntityManagerInterface $entityManager, int $id): Response
    {
        $entity = $this->repository->find($id)
            ?? throw $this->createNotFoundException();

        $entityManager->remove($entity);
        $entityManager->flush();

        // 204 No Content
        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
