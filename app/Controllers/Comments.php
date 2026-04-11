<?php

namespace App\Controllers;

use App\Models\CommentModel;
use CodeIgniter\HTTP\ResponseInterface;

class Comments extends BaseController
{
    private CommentModel $commentModel;
    private const PER_PAGE = 3;
    private array $allowedSortFields = ['id', 'created_at'];
    private array $allowedSortDirections = ['asc', 'desc'];

    public function __construct()
    {
        $this->commentModel = new CommentModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        $sort = $this->normalizeSort($this->request->getGet('sort'));
        $direction = $this->normalizeDirection($this->request->getGet('direction'));
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));

        $comments = $this->commentModel
            ->orderBy($sort, $direction)
            ->paginate(self::PER_PAGE, 'default', $page);

        $pager = $this->commentModel->pager;

        $data = [
            'title' => 'Комментарии',
            'comments' => $comments,
            'pager' => $pager,
            'sort' => $sort,
            'direction' => $direction,
        ];

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'html' => view('comments/partials/list', $data),
            ]);
        }

        return view('comments/index', $data);
    }

    public function store()
    {
        $rules = [
            'name' => [
                'label' => 'Email',
                'rules' => 'required|valid_email|max_length[255]',
            ],
            'text' => [
                'label' => 'Текст комментария',
                'rules' => 'required|min_length[3]|max_length[1000]',
            ],
            'date' => [
                'label' => 'Дата',
                'rules' => 'required|regex_match[/^\d{2}\.\d{2}\.\d{4}\s\d{2}:\d{2}$/]',
            ],
        ];

        if (! $this->validate($rules)) {
            return $this->failValidation($this->validator->getErrors());
        }

        $dateInput = trim((string) $this->request->getPost('date'));

        $date = \DateTime::createFromFormat('!d.m.Y H:i', $dateInput);
        $errors = \DateTime::getLastErrors();

        $hasDateErrors = $date === false
            || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))
            || ($date !== false && $date->format('d.m.Y H:i') !== $dateInput);

        if ($hasDateErrors) {
            return $this->failValidation([
                'date' => 'Некорректная дата. Формат: ДД.ММ.ГГГГ ЧЧ:ММ',
            ]);
        }

        $data = [
            'name' => trim((string) $this->request->getPost('name')),
            'text' => trim((string) $this->request->getPost('text')),
            'date' => trim((string) $this->request->getPost('date')),
        ];

        $this->commentModel->insert($data);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Комментарий успешно добавлен.',
        ]);
    }

    public function delete(int $id)
    {
        $comment = $this->commentModel->find($id);

        if (! $comment) {
            return $this->response
                ->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)
                ->setJSON([
                    'success' => false,
                    'message' => 'Комментарий не найден.',
                ]);
        }

        $this->commentModel->delete($id);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Комментарий успешно удалён.',
        ]);
    }

    private function normalizeSort(?string $sort): string
    {
        $sort = strtolower((string) $sort);
        return in_array($sort, $this->allowedSortFields, true) ? $sort : 'created_at';
    }

    private function normalizeDirection(?string $direction): string
    {
        $direction = strtolower((string) $direction);
        return in_array($direction, $this->allowedSortDirections, true) ? $direction : 'desc';
    }

    private function failValidation(array $errors)
    {
        return $this->response
            ->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY)
            ->setJSON([
                'success' => false,
                'errors' => $errors,
            ]);
    }
}
