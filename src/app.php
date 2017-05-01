<?php
//use Acme\HelloServiceProvider;
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

$app->get('/', function () {
    return 'Hello world';
});

/*
*	GET
*/
$app->get('/hello/{name}', function ($name) use ($app) {
    return 'Hello ' . $app->escape($name);
});

$app->get('/hello/{name}/{message}', function ($name, $message) use ($app) {
    return 'Hello ' . $app->escape($name) . ' : ' . $app->escape($message);
});

$blogPosts = array(
    1 => array(
        'date'      => '2011-03-29',
        'author'    => 'igorw',
        'title'     => 'Using Silex',
        'body'      => '...',
    ),
	2 => array(
        'date'      => '2017-06-19',
        'author'    => 'Yannick',
        'title'     => 'Test',
        'body'      => 'ok ok dbshdghsq fdsjfhsdk',
    ),
);

$app->get('/blog', function () use ($blogPosts) {
    $output = '';
	$output .= '<table border="1">';
		$output .= '<tr>';
			$output .= '<td>Titre</td>';
			$output .= '<td>Auteur</td>';
			$output .= '<td>Date</td>';
		$output .= '</tr>';
	    foreach ($blogPosts as $post) {
			$output .= '<tr>';
				$output .= '<td>'.$post['title'].'</td>';
				$output .= '<td>'.$post['author'].'</td>';
				$output .= '<td>'.date('d/m/Y', strtotime($post['date'])).'</td>';
	        $output .= '</tr>';
	    }
	$output .= '</table>';
	
    return $output;
});

$app->get('/blog/{id}', function (Silex\Application $app, $id) use ($blogPosts) {
    if (!isset($blogPosts[$id])) {
        $app->abort(404, "Post $id does not exist.");
    }

    $post = $blogPosts[$id];

    return  "<h1>{$post['title']}</h1>".
            "<p>{$post['body']}</p>";
});

/*
*	POST
*/
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->post('/feedback', function (Request $request) {
    $message = $request->get('message');
    mail('sedera.aina@yahoo.fr', '[YourSite] Feedback', $message);

    return new Response('Thank you for your feedback!', 201);
});

$app->put('/blog/{id}', function ($id) {
    // ...
});

$app->delete('/blog/{id}', function ($id) {
    // ...
});

$app->patch('/blog/{id}', function ($id) {
    // ...
});

$userProvider = function ($id) {
    return new User($id);
};

$app->get('/user/{user}', function (User $user) {
    // ...
})->convert('user', $userProvider);

$app->get('/user/{user}/edit', function (User $user) {
    // ...
})->convert('user', $userProvider);

/*
 * Default
 */
/*$app->get('/{pageName}', function ($pageName) {
    return $pageName;
})->value('pageName', 'index');*/

/*
 * Redirection
 */
$app->get('/redirection', function () use ($app) {
    return $app->redirect('/blog');
});

//use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

$app->get('/test', function () use ($app) {
    // forward to /hello
    $subRequest = Request::create('/hello/tax', 'GET');

    return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
});

/*
 * JSON
 */
$app->get('/article/{id}', function ($id) use ($app, $blogPosts) {
    $blog = $blogPosts[$id];

    if (!$blog) {
        $error = array('message' => 'The article was not found.');

        return $app->json($error, 404);
    }

    return $app->json($blog);
});

/*
 * Streaming
 */
$app->get('/images/{file}', function ($file) use ($app) {
    if (!file_exists(__DIR__.'/images/'.$file)) {
        return $app->abort(404, 'The image was not found.');
    }

    $stream = function () use ($file) {
        readfile($file);
    };
    
    /*$stream = function () {
        $fh = fopen('http://www.example.com/', 'rb');
        while (!feof($fh)) {
            echo fread($fh, 1024);
            ob_flush();
            flush();
        }
        fclose($fh);
    };*/

    return $app->stream($stream, 200, array('Content-Type' => 'image/png'));
});

/*
 * Send file
 */
$app->get('/files/{path}', function ($path) use ($app) {
    if (!file_exists(__DIR__.'/images/' . $path)) {
        $app->abort(404);
    }

    return $app->sendFile(__DIR__.'/images/' . $path);
});

$app->get('/name', function (Silex\Application $app) {
    $name = $app['request']->get('name');

    return "You provided the name {$app->escape($name)}.";
});

$app->get('/name.json', function (Silex\Application $app) {
    $name = $app['request']->get('name');

    return $app->json(array('name' => $name));
});

// define controllers for a blog
$blog = $app['controllers_factory'];
$blog->get('/', function () {
    return 'Blog home page';
});
// ...

// define controllers for a forum
$forum = $app['controllers_factory'];
$forum->get('/', function () {
    return 'Forum home page';
});

$app->mount('/blog', $blog);
$app->mount('/forum', $forum);

$app->mount('/job', include 'job.php');


/*$app->register(new Acme\HelloServiceProvider(), array(
    'hello.default_name' => 'Igor',
));

$app->get('/hello', function () use ($app) {
    $name = $app['request']->get('name');

    return $app['hello']($name);
});*/


echo '<br>';

$app['closure_parameter'] = $app->protect(function ($a, $b) {
    return $a + $b;
});

// will not execute the closure
$add = $app['closure_parameter'];

// calling it now
echo $add(2, 3);

return $app;