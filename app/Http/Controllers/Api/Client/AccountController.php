<?php

namespace Pterodactyl\Http\Controllers\Api\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Auth\AuthManager;
use Pterodactyl\Services\Users\UserUpdateService;
use Pterodactyl\Transformers\Api\Client\AccountTransformer;
use Pterodactyl\Http\Requests\Api\Client\Account\UpdateEmailRequest;
use Pterodactyl\Http\Requests\Api\Client\Account\UpdatePasswordRequest;

class AccountController extends ClientApiController
{
    private AuthManager $authManager;
    private UserUpdateService $updateService;

    /**
     * AccountController constructor.
     */
    public function __construct(AuthManager $authManager, UserUpdateService $updateService)
    {
        parent::__construct();

        $this->authManager = $authManager;
        $this->updateService = $updateService;
    }

    /**
     * Get's information about the currently authenticated user.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function index(Request $request): array
    {
        return $this->fractal->item($request->user())
            ->transformWith(AccountTransformer::class)
            ->toArray();
    }

    /**
     * Update the authenticated user's email address.
     *
     * @throws \Pterodactyl\Exceptions\Model\DataValidationException
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function updateEmail(UpdateEmailRequest $request): Response
    {
        $this->updateService->handle($request->user(), $request->validated());

        return $this->returnNoContent();
    }

    /**
     * Update the authenticated user's password. All existing sessions will be logged
     * out immediately.
     *
     * @throws \Pterodactyl\Exceptions\Model\DataValidationException
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function updatePassword(UpdatePasswordRequest $request): Response
    {
        $this->updateService->handle($request->user(), $request->validated());

        $this->authManager->logoutOtherDevices($request->input('password'));

        return $this->returnNoContent();
    }
}
