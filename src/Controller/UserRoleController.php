<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Controller;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Siganushka\GenericBundle\Exception\FormErrorException;
use Siganushka\UserBundle\Entity\UserRole;
use Siganushka\UserBundle\Form\UserRoleType;
use Siganushka\UserBundle\Repository\UserRoleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class UserRoleController extends AbstractController
{
    public function __construct(private readonly UserRoleRepository $repository)
    {
    }

    #[Route('/user-roles', methods: 'GET')]
    public function getCollection(Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $this->repository->createQueryBuilder('r');

        $page = $request->query->getInt('page', 1);
        $size = $request->query->getInt('size', 10);

        $pagination = $paginator->paginate($queryBuilder, $page, $size);

        return $this->createResponse($pagination);
    }

    #[Route('/user-roles', methods: 'POST')]
    public function postCollection(Request $request, EntityManagerInterface $entityManager): Response
    {
        $entity = $this->repository->createNew();

        $form = $this->createForm(UserRoleType::class, $entity);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            throw new FormErrorException($form);
        }

        $entityManager->persist($entity);
        $entityManager->flush();

        return $this->createResponse($entity);
    }

    #[Route('/user-roles/{id<\d+>}', methods: 'GET')]
    public function getItem(int $id): Response
    {
        $entity = $this->repository->find($id);
        if (!$entity) {
            throw $this->createNotFoundException(\sprintf('Resource #%d not found.', $id));
        }

        return $this->createResponse($entity);
    }

    #[Route('/user-roles/{id<\d+>}', methods: ['PUT', 'PATCH'])]
    public function putItem(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $entity = $this->repository->find($id);
        if (!$entity) {
            throw $this->createNotFoundException(\sprintf('Resource #%d not found.', $id));
        }

        $form = $this->createForm(UserRoleType::class, $entity);
        $form->submit($request->request->all(), !$request->isMethod('PATCH'));

        if (!$form->isValid()) {
            throw new FormErrorException($form);
        }

        $entityManager->flush();

        return $this->createResponse($entity);
    }

    #[Route('/user-roles/{id<\d+>}', methods: 'DELETE')]
    public function deleteItem(EntityManagerInterface $entityManager, int $id): Response
    {
        $entity = $this->repository->find($id);
        if (!$entity) {
            throw $this->createNotFoundException(\sprintf('Resource #%d not found.', $id));
        }

        try {
            $entityManager->remove($entity);
            $entityManager->flush();
        } catch (ForeignKeyConstraintViolationException) {
            throw new BadRequestHttpException('Unable to delete resource.');
        }

        // 204 No Content
        return $this->createResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param PaginationInterface<int, mixed>|UserRole|null $data
     */
    protected function createResponse(PaginationInterface|UserRole|null $data, int $statusCode = Response::HTTP_OK, array $headers = []): Response
    {
        $attributes = ['id', 'name', 'permissions', 'updatedAt', 'createdAt'];

        return $this->json($data, $statusCode, $headers, compact('attributes'));
    }
}
