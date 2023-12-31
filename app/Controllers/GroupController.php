<?php

namespace Kento1221\UserUsergroupCrudApp\Controllers;

use Kento1221\UserUsergroupCrudApp\Models\User;
use Kento1221\UserUsergroupCrudApp\Models\UserGroup;
use Kento1221\UserUsergroupCrudApp\Validators\StoreGroupRequestValidator;
use Kento1221\UserUsergroupCrudApp\Validators\UpdateGroupRequestValidator;

class GroupController extends Controller
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

        $groups = $this->userGroupModel
            ->with(['created_at', 'updated_at'])
            ->get(self::LIST_MAX_ITEMS_PER_PAGE, self::LIST_MAX_ITEMS_PER_PAGE * ($page - 1));

        $nextPageCount = count($this->userGroupModel->get(self::LIST_MAX_ITEMS_PER_PAGE, self::LIST_MAX_ITEMS_PER_PAGE * $page));

        $this->assignData([
            'groups'     => $groups,
            'page'       => $page,
            'isNextPage' => $nextPageCount > 0
        ]);

        $this->render('group/list');
    }

    public function edit()
    {
        try {
            $id = filter_input(INPUT_GET, 'groupId', FILTER_VALIDATE_INT) ?? -1;

            $this->assignData([
                'group' => $this->userGroupModel->find($id)
            ]);

            $this->render('group/edit');

        } catch (\Exception $exception) {
            $this->render('404');
        }
    }

    public function update()
    {
        try {
            $data = UpdateGroupRequestValidator::validate();

            $group = $this->userGroupModel->find($data['id']);
            $updated = $group->update($data);

            $this->jsonResponse([
                'success' => $updated,
                'message' => $updated ? 'The group has been updated successfully!' : 'The group could not be updated.'
            ]);

        } catch (\Exception $exception) {
            $this->jsonResponse([
                'success' => false,
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function create()
    {
        $this->render('group/new');
    }

    public function store()
    {
        try {
            $data = StoreGroupRequestValidator::validate();

            $createdGroupId = $this->userGroupModel->create($data);
            $createdGroup = $this->userGroupModel->find($createdGroupId);

            $this->jsonResponse([
                'success' => true,
                'group'   => $createdGroup,
                'message' => 'New group has been created successfully.'
            ]);

        } catch (\Exception $exception) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Unfortunately, new group could not be created.'
            ]);
        }
    }

    public function delete()
    {
        $id = filter_input(INPUT_GET, 'groupId', FILTER_VALIDATE_INT);

        if (!$id) {

            $this->jsonResponse([
                'success' => false,
                'message' => 'Missing groupId parameter.'
            ]);

            die(422);
        }

        try {
            $group = $this->userGroupModel->find($id);
            $deleted = $group->delete();

            $this->jsonResponse([
                'success' => $deleted,
                'message' => $deleted ? 'The group has been deleted successfully!' : 'The group could not be deleted.'
            ]);

        } catch (\Throwable $exception) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'The group could not be deleted.'
            ]);
        }
    }
}