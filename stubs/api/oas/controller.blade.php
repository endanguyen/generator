namespace App\Http\Controllers\Api\{{ $versionNamespace }};

use App\Exceptions\Api\{{ $versionNamespace }}\APIErrorException;
use App\Http\Controllers\Controller;
use App\Services\APIUserServiceInterface;
use App\Services\FileServiceInterface;
@foreach( $controller->getRequiredRepositoryNames() as $name )
use App\Repositories\{{ $name }}Interface;
@endforeach
@foreach( $controller->getRequiredResponseNames() as $name )
use App\Http\Responses\Api\{{ $versionNamespace }}\{{ $name }};
@endforeach
@foreach( $controller->getRequiredRequestNames() as $name )
use App\Http\Requests\Api\{{ $versionNamespace }}\{{ $name }};
@endforeach
@if( ends_with($className, 'AuthController'))
use App\Repositories\UserRepositoryInterface;
use App\Services\UserServiceAuthenticationServiceInterface;
use League\OAuth2\Server\AuthorizationServer;
use Zend\Diactoros\Response as Psr7Response;
@endif

class {{ $className }} extends Controller
{
    /** @var APIUserServiceInterface $userService */
    protected $userService;

    /** @var FileServiceInterface $fileService */
    protected $fileService;

@if( ends_with($className, 'AuthController'))
    /** @var APIUserServiceInterface $authenticatableService */
    protected $authenticatableService;

    /** @var UserServiceAuthenticationServiceInterface $serviceAuthenticationService */
    protected $serviceAuthenticationService;

    /** @var UserRepositoryInterface $userRepository */
    protected $userRepository;
@endif

@foreach( $controller->getRequiredRepositoryNames() as $name )
    /** @var {{ $name }}Interface ${{ lcfirst($name) }} */
    protected ${{ lcfirst($name) }};
@endforeach

    public function __construct(
@foreach( $controller->getRequiredRepositoryNames() as $name )
        {{ $name }}Interface ${{ lcfirst($name) }},
@endforeach
@if( ends_with($className, 'AuthController'))
        UserServiceAuthenticationServiceInterface $serviceAuthenticationService,
        AuthorizationServer $server,
        UserRepositoryInterface $userRepository,
@endif
        APIUserServiceInterface $userService,
        FileServiceInterface $fileService
    ) {
@foreach( $controller->getRequiredRepositoryNames() as $name )
        $this->{{ lcfirst($name) }} = ${{ lcfirst($name) }};
@endforeach
        $this->userService        = $userService;
        $this->fileService        = $fileService;
@if( ends_with($className, 'AuthController'))
        $this->authenticatableService       = $authenticatableService;
        $this->serviceAuthenticationService = $serviceAuthenticationService;
        $this->server                       = $server;
        $this->userRepository               = $userRepository;
@endif
    }

@foreach( $controller->getActions() as $action )
@include('api.oas.actions.' . $action->getType())

@endforeach
}
