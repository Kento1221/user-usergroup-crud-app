<?php
declare(strict_types=1);

namespace Kento1221\UserUsergroupCrudApp\Controllers;

use Kento1221\UserUsergroupCrudApp\Models\User;
use Kento1221\UserUsergroupCrudApp\Models\UserGroup;
use Kento1221\UserUsergroupCrudApp\Validators\StoreUserRequestValidator;
use Kento1221\UserUsergroupCrudApp\Validators\UpdateUserRequestValidator;

class UserController extends Controller
{
    protected User      $user;
    protected UserGroup $userGroup;

    public function __construct()
    {
        parent::__construct();
        $this->user = new User();
        $this->userGroup = new UserGroup();
    }

    public function index()
    {
        $this->list();
    }

    public function list()
    {
        $limit = 10;
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?? 1;

        $users = $this->user->with(['created_at', 'updated_at'])
                            ->get($limit, $limit * ($page - 1));
        $nextPageCount = count($this->user->get($limit, $limit * $page));

        $this->assignData([
            'users'      => $users,
            'page'       => $page,
            'isNextPage' => $nextPageCount > 0
        ]);

        $this->render('user/list');
    }

    public function edit()
    {
        try {
            $id = filter_input(INPUT_GET, 'userId', FILTER_VALIDATE_INT) ?? -1;

            /** @var User $user */
            $user = $this->user->getById($id);

            $this->assignData([
                'user'       => $user,
                'userGroups' => $user->groups(),
                'groups'     => $this->userGroup->getAll()
            ]);

            $this->render('user/edit');

        } catch (\Exception $exception) {
            $this->render('404');
        }
    }

    public function update()
    {
        try {
            $data = UpdateUserRequestValidator::validate();
            $updated = $this->user->update($data['id'], $data);
            $user = $this->user->getById($data['id']);

            $synced = $user->sync(
                $data['groups'],
                'user_user_groups',
                'user_id',
                'user_group_id'
            );

            $this->jsonResponse([
                'success' => $updated && $synced,
                'message' => $updated && $synced ? 'The user has been updated successfully!' : 'The user could not be updated.'
            ]);

        } catch (\Exception $exception) {
            $this->jsonResponse([
                'success' => false,
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function delete()
    {
        $id = filter_input(INPUT_GET, 'userId', FILTER_VALIDATE_INT);

        if (!$id) {

            $this->jsonResponse([
                'success' => false,
                'message' => 'Missing userId parameter.'
            ]);

            die(422);
        }

        try {
            $deleted = $this->user->delete($id);
        } catch (\Throwable $exception) {
            die();
        }

        $this->jsonResponse([
            'success' => $deleted,
            'message' => $deleted ? 'The user has been deleted successfully!' : 'The user could not be deleted.'
        ]);
    }

    public function create()
    {
        $this->render('user/new');
    }

    public function store()
    {
        try {
            $data = StoreUserRequestValidator::validate();
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);

            $createdUser = $this->user->create($data);

            $this->jsonResponse([
                'success' => true,
                'user'    => $createdUser,
                'message' => 'New user has been created successfully.'
            ]);

        } catch (\Exception $exception) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Unfortunately, new user could not be created.'
            ]);
        }
    }
}