# README #

This README would normally document whatever steps are necessary to get your application up and running.

### What is this repository for? ###

making lumen api with oauth.

### How do I get set up? ###

configure oauth LucaDegasperi oauth server.
copy migration file from LucaDegasperi/databse/migration folder to root/database/migration folder.
copy config folder from LucaDegasperi/config to root folder.
Create table by php artisan migrate command.
Configure bootstrap/index.php. uncomment 
$app->withFacades();
$app->withEloquent();
and provide routes.php path.

give access/ability to user/public from app/providers/AuthorizeServiceProvider.php so that what api can understand which method is public or which associated with paricular user.

like User Creation method is public but User update method is must belong to particular user.

Add below code in App/httpd/Controllers/Controller.php so that we can get userId by simply calling Controller::getUserId() it 
will give us current user Id.

protected function getUserId(){
        return \LucaDegasperi\OAuth2Server\Facades\Authorizer::getResourceOwnerId();
}

Create model for each table in App folder.
Create Controller for each model in App/httpd/Controllers.
Create routes in routes.php file for each controller method. So that particular CRUD operation will handle.

ex. In User Controller we have save method.
public function save(Request $request){
}

use in route.php ==>
$app->post('user/store',UserController@save);

Now every post request coming to user/store is hadle by save method of UserController.

To get one to many relationship data :
ControllerName::find($id)->hasManymethod();

for twilio add twilio/sdk in required section of composer.json.
run composer update command, then composer dumpautoload. After that you can find twilio folder inside vendor.
then create folder inside App and copy and paste appropriate code for sending message using twilio rest in a file.
add required_once path of autoload.php (it is in twilio folder).
base_path() gives you absolute path. (base_path().'/vendor/twilio/sdk/Twilio/autoload.php')

// cors header fixed

By Creating CorsMiddleware in App/http i have fix this.
After creating CorsMiddleware i add it in app.php.like below :
$app->middleware([
    App\Http\Middleware\CorsMiddleware::class,
]);

need to be check
=> use App/Contact in AuthorizeServiceProvider so Contact can be authorize.
### Contribution guidelines ###

* Writing tests
* Code review
* Other guidelines

### Who do I talk to? ###

* Repo owner or admin
* Other community or team contact