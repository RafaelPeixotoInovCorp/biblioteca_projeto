protected $routeMiddleware = [
// ... outros middlewares ...
'permission' => \App\Http\Middleware\CheckPermission::class,
];
