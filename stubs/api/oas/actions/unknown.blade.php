    /**
    * PATH: {{ $action->getHttpMethod() }} {{ $action->getPath() }}
@foreach( $action->getParams() as $param )
        * @param mixed {{ $param }}
@endforeach
    * @param {{ $action->getRequest()->getName() }} $request
    *
    * @return \Illuminate\Http\JsonResponse
    * @throws \App\Exceptions\Api\{{ $versionNamespace }}\APIErrorException
    */
    public function {{ $action->getMethod() }}({{ implode(',', $action->getParams() ) }}{{ count($action->getParams()) > 0 ? ', ' : '' }}{{ $action->getRequest()->getName() }} $request)
    {
        /** @var \App\Models\User $user */
        $user = $this->userService->getUser();

        // [TODO] Need to implement

        $dataArray = [
        @foreach($action->getResponse()->getProperties() as $property )
            '{{ $property['name'] }}' => $request->get('{{ $property['name'] }}',{!! $property['default'] !!}),
        @endforeach
        ];
        $response = new {!! $action->getResponse()->getName() !!}($dataArray, 200);

        return $response->response();
    }
