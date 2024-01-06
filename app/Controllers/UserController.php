<?php
declare(strict_types=1);

namespace Kento1221\UserUsergroupCrudApp\Controllers;

use Kento1221\UserUsergroupCrudApp\Models\User;
use Kento1221\UserUsergroupCrudApp\Models\UserGroup;
use Kento1221\UserUsergroupCrudApp\Validators\StoreUserRequestValidator;
use Kento1221\UserUsergroupCrudApp\Validators\UpdateUserRequestValidator;

class UserController extends Controller
{
    const LIST_MAX_ITEMS_PER_PAGE = 10;

    protected User      $userModel;
    protected UserGroup $userGroupModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->userGroupModel = new UserGroup();
    }

    public function index()
    {
        $this->list();
    }

    public function list()
    {
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?? 1;

        $users = $this->userModel
            ->with(['created_at', 'updated_at'])
            ->get(self::LIST_MAX_ITEMS_PER_PAGE, self::LIST_MAX_ITEMS_PER_PAGE * ($page - 1));

        $nextPageCount = count($this->userModel->get(self::LIST_MAX_ITEMS_PER_PAGE, self::LIST_MAX_ITEMS_PER_PAGE * $page));

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
            $user = $this->userModel->find($id);

            $this->assignData([
                'user'       => $user,
                'userGroups' => $user->groups(),
                'groups'     => $this->userGroupModel->getAll()
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

            $user = $this->userModel->find($data['id']);
            $updated = $user->update($data);

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
            $user = $this->userModel->find($id);
            $deleted = $user->delete();

            $this->jsonResponse([
                'success' => $deleted,
                'message' => $deleted ? 'The user has been deleted successfully!' : 'The user could not be deleted.'
            ]);

        } catch (\Throwable $exception) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'The user could not be deleted.'
            ]);
        }
    }

    public function create()
    {
        $this->assignData([
            'groups' => $this->userGroupModel->getAll()
        ]);

        $this->render('user/new');
    }

    public function store()
    {
        try {
            $data = StoreUserRequestValidator::validate();
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);

            $createdUserId = $this->userModel->create($data);
            $createdUser = $this->userModel->find($createdUserId);

            $appended = $createdUser->appendMany(
                $data['groups'],
                'user_user_groups',
                'user_id',
                'user_group_id'
            );

            $this->jsonResponse([
                'success' => true,
                'user'    => $createdUser,
                'message' => 'New user has been created successfully.'
                    . (!$appended && 'Unfortunately, groups could not be appended to the user.')
            ]);

        } catch (\Exception $exception) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Unfortunately, new user could not be created.'
            ]);
        }
    }

    public function listByGroup()
    {
        try {
            $groupId = filter_input(INPUT_GET, 'groupId', FILTER_VALIDATE_INT);

            $group = $this->userGroupModel->find($groupId);

            $this->assignData([
                'users' => $group->users(),
                'group' => $group
            ]);

            $this->render('user/listByGroup');

        } catch (\Exception $exception) {
            $this->render('404');
        }
    }
}