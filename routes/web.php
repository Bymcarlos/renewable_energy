	<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//Don't need registration, so comment the Auth:routes() method, and add only login/pass routes:
//Auth::routes();

//Open the vendor/laravel/framework/src/Illuminate/Routing/Router.php file and find the method called auth(). 
//This is what Auth::routes() was calling behind the scenes:

//Login routes:
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

/* Registration Routes...
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');*/

// Password Reset Routes...
/*
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');
*/
Route::get('password/forgot', 'UserController@forgotPassword')->name('password.forgot');

Route::get('/home', 'HomeController@index')->name('home');

//Admin routes:
Route::group(['middleware' => 'auth'], function () {
	Route::get('management', function () {
	    return view('intranet.management');
	})->name("management");

	Route::get('reports', function () {
	    return view('intranet.reports');
	})->name("reports");

	Route::resource('entities','EntityController')->middleware('role:admin');
	Route::get('entities_export','EntityController@Exc_Entities')->name('entities.export')->middleware('role:admin');

	Route::resource('staff','StaffController')->middleware('role:admin');
	Route::resource('areas','AreaController')->middleware('role:admin');
	//Route for components dynamic load when select an area in the form of a new Bench: (json response)
	Route::post('area/components','AreaController@list')->middleware('role:admin,editor,user');

	Route::resource('components','ComponentController')->middleware('role:admin');
	Route::get('component/{component_id}/{area_id}','ComponentController@componentArea')->name('component.area.update')->middleware('role:admin');
	//To edit component, get current list areas asociated with de component
	Route::post('component/areas','ComponentController@list')->middleware('role:admin');
	Route::get('components_export','ComponentController@Exc_Components')->name('components.export')->middleware('role:admin');

	//Important! this routes can not be accessibles for users without permissions, so, independent routes to check user role:
	Route::get('assessment_tech','AssessmentController@indexTech')->name('assessments.technical')->middleware('role:admin');
	Route::get('assessment_econ','AssessmentController@indexEcon')->name('assessments.economical')->middleware('role:admin');
	//Invalid route (must be one of the previous, to diference between technical and economical assesstments):
	Route::get('assessments',['as' => 'assessments.index', 'uses' => 'HomeController@routeError']);
	Route::resource('assessments','AssessmentController', ['except' => ['index']])->middleware('role:admin');

	//Important! Check if the user has role to request for this sheet:
	Route::get('sheets/{id}', ['as' => 'sheets.index', 'uses' => 'SheetController@index'])->middleware('role:admin');
	Route::resource('sheets', 'SheetController', ['except' => ['index']])->middleware('role:admin');

	Route::get('features/{id}/{cat?}/{subcat?}', ['as' => 'features.index', 'uses' => 'FeatureController@index'])->middleware('role:admin');
	Route::resource('features', 'FeatureController', ['except' => ['index']])->middleware('role:admin');

	Route::resource('cats','CatController')->middleware('role:admin');
	Route::resource('subcats','SubcatController')->middleware('role:admin');
	
	Route::get('benches/excel','BenchController@Exc_benchesExport')->name('benches.export.excel')->middleware('role:admin');
	Route::get('bench/export/{id}','BenchController@benchExport')->name('bench.export')->middleware('role:admin,editor');
	Route::post('bench/import/{id}','BenchController@benchImport')->name('bench.import')->middleware('role:admin,editor');
	Route::resource('benches','BenchController')->middleware('role:admin,editor');

	//Important! this routes can not be accessibles for users without permissions, so, independent routes to check user role:
	Route::get('bench_assessment_tech/{bench}/{imported?}','BenchController@showTech')->name('bench.assessments.technical');
	Route::get('bench_assessment_econ/{bench}/{imported?}','BenchController@showEcon')->name('bench.assessments.economical')->middleware('role:admin');
	//Export to excel (Also independent routes to protect from users without permissions):
	Route::get('bench_assessments_tech/excel/{bench}/{sheet?}/{cat?}/{subcat?}','BenchController@Exc_benchAssTechExport')->name('bench.assessments.technical.export.excel');
	Route::get('bench_assessments_econ/excel/{bench}','BenchController@Exc_benchAssEconExport')->name('bench.assessments.economical.export.excel')->middleware('role:admin');

	Route::resource('unittypes','UnittypeController');
	//Route for units dynamic load when select an unittype in the form of benchtechsheetfeatures: (json response)
	Route::post('unittype/units','UnittypeController@list');

	Route::get('units/{id}', ['as' => 'units.index', 'uses' => 'UnitController@index'])->middleware('role:admin');
	Route::resource('units', 'UnitController', ['except' => ['index']])->middleware('role:admin');

	Route::resource('users','UserController')->middleware('role:admin');
	Route::put('user/password/{id}','UserController@passwordChange')->name('users.password.change')->middleware('role:admin');

	Route::resource('platforms','PlatformController')->middleware('role:admin,editor');
	Route::get('products/{id}', ['as' => 'products.index', 'uses' => 'ProductController@index'])->middleware('role:admin,editor');
	Route::resource('products', 'ProductController', ['except' => ['index']])->middleware('role:admin,editor');

	Route::get('benchfeatures/{bench}/{sheet}/{cat?}/{subcat?}','BenchFeatureController@index')->name('benchfeatures.index')->middleware('role:admin,editor');
	Route::post('benchfeatures','BenchFeatureController@update')->name('benchfeatures.value')->middleware('role:admin,editor');
	Route::get('benchfeatures/{bench}/{sheet}/{cat}/{subcat}/{feature}','BenchFeatureController@featureState')->name('benchfeatures.state')->middleware('role:admin,editor');

	Route::post('benchfeature/attach','BenchFeatureController@attach')->name('benchfeatures.attach')->middleware('role:admin,editor');
	Route::delete('benchfeature/detach','BenchFeatureController@detach')->name('benchfeatures.detach')->middleware('role:admin,editor');

	Route::post('benchfeature/brand','BenchFeatureController@brandAdd')->name('benchfeatures.brand.store')->middleware('role:admin,editor');
	Route::put('benchfeature/brand','BenchFeatureController@brandUpdate')->name('benchfeatures.brand.update')->middleware('role:admin,editor');
	Route::delete('benchfeature/brand','BenchFeatureController@brandDelete')->name('benchfeatures.brand.delete')->middleware('role:admin,editor');

	Route::get('occupation/export/{bench}', 'OccupationController@Exc_occupationExport')->name('occupation.bench.export.excel')->middleware('role:admin');
	Route::get('occupation/{bench}/{year?}', ['as' => 'occupation.index', 'uses' => 'OccupationController@index'])->middleware('role:admin');
	Route::resource('occupation', 'OccupationController', ['except' => ['index']])->middleware('role:admin');
	Route::put('occupation_year', 'OccupationController@addYear')->name('occupation.year.add')->middleware('role:admin');
	Route::delete('occupation_year', 'OccupationController@deleteYear')->name('occupation.year.delete')->middleware('role:admin');

	//Reports:
	Route::get('rep_benches','BenchController@Rep_Benches')->name('benches.reports.index');

	//Important! this routes can not be accessibles for users without permissions, so, independent routes to check user role:
	Route::get('rep_bench_assessment_tech/{bench}','BenchController@Rep_showTech')->name('bench.reports.assessments.technical');
	Route::get('rep_bench_assessment_econ/{bench}','BenchController@Rep_showEcon')->name('bench.reports.assessments.economical')->middleware('role:admin');
	Route::get('rep_bench_occupation/{bench}/{year?}','BenchController@Rep_Occupation')->name('bench.reports.occupation')->middleware('role:admin');
	Route::get('rep_benchfeatures/{bench}/{sheet}/{cat?}/{subcat?}','BenchFeatureController@Rep_BenchFeatures')->name('benchfeatures.reports.index');

	//Report -> Entities by technical sheet
	Route::get('rep_entitiesbytechsheet','BenchController@Rep_EntitiesTechSheet')->name('benches.reports.entitiesbytechsheet');
	Route::post('rep_entitiesbytechsheet','BenchController@Rep_EntitiesTechSheet')->name('benches.reports.entitiesbytechsheet.filter');
	Route::get('rep_entitiesbytechsheet/export/{area}/{component}','BenchController@Exc_EntitiesTechSheet')->name('benches.reports.entitiesbytechsheet.export.excel');

	//Report -> Search by parameters
	Route::get('rep_parameters','BenchController@Rep_Parameters')->name('benches.reports.parameters');
	Route::post('rep_parameters','BenchController@Rep_ParametersStore')->name('benches.reports.parameters.store');
	Route::put('rep_parameters/{bench}','BenchController@Rep_ParametersUpdate')->name('benches.reports.parameters.update');
	Route::delete('rep_parameters/{bench}','BenchController@Rep_ParametersDestroy')->name('benches.reports.parameters.destroy');

	Route::get('rep_parameters/{bench}','BenchController@Rep_ParametersBench')->name('benches.reports.parameters.bench');
	Route::get('rep_parameters/{bench}/{sheet}/{cat?}/{subcat?}','BenchFeatureController@Rep_ParametersBenchFeatures')->name('benches.reports.parameters.features');
	Route::post('rep_parameters_feature','BenchFeatureController@Rep_ParametersBenchFeatureUpdate')->name('benches.reports.parameters.feature.value');
	Route::get('rep_parameters_show/{bench}','BenchController@Rep_ParametersShow')->name('benches.reports.parameters.show');
	Route::get('rep_parameters_show/{bench}/{sheet}/{cat?}/{subcat?}','BenchFeatureController@Rep_ParametersShowFeatures')->name('benches.reports.parameters.show.features');
	Route::get('rep_parameters_export/excel/{bench}/{sheet?}/{cat?}/{subcat?}','BenchFeatureController@Exc_ParametersShowFeatures')->name('benches.reports.parameters.show.export.excel');
	//Route::get('rep_parameters/{area?}/{component?}/{cat?}/{subcat?}','BenchController@Rep_Parameters')->name('benches.reports.parameters');

	//Report ->Component Occupation
	Route::get('rep_occupationcomponent', 'OccupationController@Rep_OccupationComponent')->name('benches.reports.occupationcomponent');
	Route::post('rep_occupationcomponent', 'OccupationController@Rep_OccupationComponent')->name('benches.reports.occupationcomponent.filter');
	Route::get('rep_occupationcomponent/export/{component}/{year}/{week_from}/{week_to}', 'OccupationController@Exc_OccupationComponent')->name('benches.reports.occupationcomponent.export.excel');

	//Report ->Entity Occupation
	Route::get('rep_occupationentity', 'OccupationController@Rep_OccupationEntity')->name('benches.reports.occupationentity');
	Route::post('rep_occupationentity', 'OccupationController@Rep_OccupationEntity')->name('benches.reports.occupationentity.filter');
	Route::get('rep_occupationentity/export/{entity}/{year}', 'OccupationController@Exc_OccupationEntity')->name('benches.reports.occupationentity.export.excel');

	//V2: Rating Tools
	Route::get('ratingtools', function () {
	    return view('ratingtools.main');
	})->name("ratingtools")->middleware('role:admin,editor');
	
	//Templates:
	Route::get('ratingtools/templates', function () {
	    return view('ratingtools.templates.main');
	})->name("ratingtools.templates")->middleware('role:admin,editor');

	Route::get('inputsheetsareas','InputsheetController@areas')->name('inputsheets.areas')->middleware('role:admin,editor');
	Route::get('inputsheets/{area}', ['as' => 'inputsheets.index', 'uses' => 'InputsheetController@index'])->middleware('role:admin,editor');
	Route::resource('inputsheets', 'InputsheetController', ['except' => ['index']])->middleware('role:admin,editor');

	Route::resource('inputcats', 'InputcatController')->middleware('role:admin,editor');

	Route::get('inputrequests/{inputsheet}/{inputcat?}', ['as' => 'inputrequests.index', 'uses' => 'InputrequestController@index'])->middleware('role:admin,editor');
	Route::resource('inputrequests', 'InputrequestController', ['except' => ['index']])->middleware('role:admin,editor');

	Route::get('techsheetsareas','TechsheetController@areas')->name('techsheets.areas')->middleware('role:admin,editor');
	Route::get('techsheets/{area}', ['as' => 'techsheets.index', 'uses' => 'TechsheetController@index'])->middleware('role:admin,editor');
	Route::resource('techsheets', 'TechsheetController', ['except' => ['index']])->middleware('role:admin,editor');

	//To get (criticality/weight) pairs in techsheet template list (to define in a technical template)
	Route::get('techsheet/criticalities/weights/{techsheet}','TechsheetController@getCriticalitiesWeights')->name('techsheet.criticalities.weights');
	Route::post('techsheet/criticalities/weights/{techsheet}','TechsheetController@setCriticalitiesWeights')->name('techsheet.criticalities.weights');

	Route::resource('techcats', 'TechcatController')->middleware('role:admin,editor');

	//Route for feature dynamic load when select assessment/sheet: (json response)
	Route::get('assessment/sheet/{id}','SheetController@listSheetCatsSubcats');
	Route::get('cat/subcat/{id}','SheetController@listSubcatFeatures');

	//Route for load criteriafuncs from feature response type and criticality:
	Route::post('criteriafuncs','TechrequestController@getCriteriaFuncs');

	//For change techcat applicable state:
	Route::post('techcat/applicable','TechrequestController@changeApplicable')->name('techcat.applicable')->middleware('role:admin,editor');

	Route::get('techrequests/{techsheet}/{techcat?}', ['as' => 'techrequests.index', 'uses' => 'TechrequestController@index'])->middleware('role:admin,editor');
	Route::resource('techrequests', 'TechrequestController', ['except' => ['index']])->middleware('role:admin,editor');

	Route::resource('timesheets', 'TimesheetController')->middleware('role:admin,editor');
	//To get/set timecats (Availability, Executions, Flexibility) weights for a timesheet template
	Route::get('timecats/weight/{timesheet_id}','TimesheetController@getCategoriesWeight')->name('timesheet.timecats.weight');
	Route::post('timecats/weight/{timesheet_id}','TimesheetController@setCategoriesWeight')->name('timesheet.timecats.weight');

	Route::get('timerequests/{timesheet}/{timecat?}/{timesubcat?}', ['as' => 'timerequests.index', 'uses' => 'TimerequestController@index'])->middleware('role:admin,editor');
	Route::resource('timerequests', 'TimerequestController', ['except' => ['index']])->middleware('role:admin,editor');
	//To get (percent/value) pairs in timerequest settable fields (at the moment only in 'Availability / General / Number of weeks delay...'' of a timesheet)
	Route::get('timerequestsetts/{timerequest}','TimerequestController@getTimerequestSetts')->name('timerequest.timerequestsetts');
	Route::post('timerequestsetts/{timerequest}','TimerequestController@setTimerequestSetts')->name('timerequest.timerequestsetts');

	Route::resource('economicsheets', 'EconomicsheetController')->middleware('role:admin,editor');

	Route::get('economicrequests/{economicsheet}/{economiccat?}/{economicsubcat?}', ['as' => 'economicrequests.index', 'uses' => 'EconomicrequestController@index'])->middleware('role:admin,editor');
	Route::resource('economicrequests', 'EconomicrequestController', ['except' => ['index']])->middleware('role:admin,editor');
	//Set economic request weight:
	Route::put('economicrequest/weight/{economicrequest}', 'EconomicrequestController@setWeight');

	//Ratings:
	Route::get('ratingsareas','RatingController@areas')->name('ratings.areas')->middleware('role:admin,editor');
	Route::get('ratings/{area}', ['as' => 'ratings.index', 'uses' => 'RatingController@index'])->middleware('role:admin,editor');
	Route::get('rating/{rating}', ['as' => 'ratings.show', 'uses' => 'RatingController@show'])->middleware('role:admin,editor');
	Route::resource('ratings', 'RatingController', ['except' => ['index','show']])->middleware('role:admin,editor');
	
	//Route for techsheets dynamic load when select an area in the form of a new Rating: (json response)
	//Route::post('area/techsheets','AreaController@listTechsheets');

	Route::get('ratinginputrequests/{rating}/{inputcat?}', ['as' => 'ratinginputrequests.index', 'uses' => 'RatinginputrequestController@index'])->middleware('role:admin,editor');
	Route::resource('ratinginputrequests','RatinginputrequestController', ['except' => ['index']])->middleware('role:admin,editor');

	//To change ratingtechcat applicable state:
	Route::post('ratingtechcat/applicable','RatingtechrequestController@changeApplicable')->name('ratingtechcat.applicable')->middleware('role:admin,editor');
	//To change ratingtechrequest critilicality:
	Route::post('ratingtechrequest/criticality','RatingtechrequestController@changeCriticality')->name('ratingtechrequest.criticality')->middleware('role:admin,editor');

	Route::get('ratingtechrequests/{rating}/{techcat?}', ['as' => 'ratingtechrequests.index', 'uses' => 'RatingtechrequestController@index'])->middleware('role:admin,editor');
	Route::resource('ratingtechrequests','RatingtechrequestController', ['except' => ['index']])->middleware('role:admin,editor');

	Route::get('ratingbenches/{rating}', ['as' => 'ratingbenches.index', 'uses' => 'RatingbenchController@index'])->middleware('role:admin,editor');
	Route::resource('ratingbenches','RatingbenchController', ['except' => ['index']])->middleware('role:admin,editor');
	//Routes for benches selection:
	Route::get('ratingbenchesselection/{rating}','RatingbenchController@ratingBenchesSelection')->name('ratingbenchesselection')->middleware('role:admin,editor');
	Route::post('ratingbenchesselection/{rating}/{component?}','RatingbenchController@ratingBenchesSelection')->name('ratingbenchesselection.component')->middleware('role:admin,editor');
	Route::get('ratingbenchesselection/{rating}/{bench}/{component}/{ratingbench}','RatingbenchController@ratingBenchState')->name('ratingbenchesselection.state')->middleware('role:admin,editor');

	Route::get('ratingtimerequests/{ratingbench}/{timecat?}/{timesubcat?}', ['as' => 'ratingtimerequests.index', 'uses' => 'RatingtimerequestController@index'])->middleware('role:admin,editor');
	Route::resource('ratingtimerequests','RatingtimerequestController', ['except' => ['index']])->middleware('role:admin,editor');

	Route::get('ratingeconomicrequests/{ratingbench}/{economiccat?}/{economicsubcat?}', ['as' => 'ratingeconomicrequests.index', 'uses' => 'RatingeconomicrequestController@index'])->middleware('role:admin,editor');
	Route::resource('ratingeconomicrequests','RatingeconomicrequestController', ['except' => ['index']])->middleware('role:admin,editor');

	//Scores:
	//Export to excel (inputdata and technical)
	Route::get('scores/technical/excel/{rating}/{techcat?}','ScoresController@Exc_technical')->name('scores.technical.excel')->middleware('role:admin,editor');
	//Show technical scores:
	Route::get('scores/technical/{rating}/{techcat?}','ScoresController@technical')->name('scores.technical')->middleware('role:admin,editor');
	
	//Export to excel (timing)
	Route::get('scores/timing/excel/{rating}/{cat_type?}','ScoresController@Exc_timing')->name('scores.timing.excel')->middleware('role:admin,editor');
	//Show timing scores:
	Route::get('scores/timing/{rating}/{cat_type?}','ScoresController@timing')->name('scores.timing')->middleware('role:admin,editor');

	//Export to excel (economics)
	Route::get('scores/economics/excel/{rating}/{cat_type?}','ScoresController@Exc_economics')->name('scores.economics.excel')->middleware('role:admin,editor');
	//Show economics scores:
	Route::get('scores/economics/{rating}/{cat_type?}','ScoresController@economics')->name('scores.economics')->middleware('role:admin,editor');
	
	
	//Create excel file with full rating data and store on server:
	Route::post('scores/store','ScoresController@store')->name('scores.store.rating')->middleware('role:admin,editor');
	Route::get('ratingreports','RatingfileController@areas')->name('ratingreports.index')->middleware('role:admin,editor');
	
	Route::get('ratingfiles/{area}', ['as' => 'ratingfiles.index', 'uses' => 'RatingfileController@index'])->middleware('role:admin,editor');
	Route::resource('ratingfiles','RatingfileController', ['except' => ['index']])->middleware('role:admin,editor');

	Route::post('ratingfile/status','RatingfileController@statusChange')->name('ratingfile.status')->middleware('role:admin,editor');

	//PARTNERS ROUTES:
	Route::get('partnersscopes','PartnerController@scopes')->name('partners.scopes')->middleware('role:admin,editor');
	Route::get('partners/{scope}', ['as' => 'partners.index', 'uses' => 'PartnerController@index'])->middleware('role:admin,editor');
	Route::resource('partners', 'PartnerController', ['except' => ['index']])->middleware('role:admin,editor');

	Route::get('partner/{partner_id}/{sheet_id}/{section_id?}','PartnerController@sheet')->name('partner.sheet')->middleware('role:admin,editor');
	Route::put('partner/request','PartnerController@setValue')->name('partner.request')->middleware('role:admin,editor');
	Route::resource('scopes','ScopeController');

	Route::resource('sections', 'SectionController')->middleware('role:admin');

	Route::get('generalrequests/{generalsheet_id}/{section_id?}', ['as' => 'generalrequests.index', 'uses' => 'GeneralrequestController@index'])->middleware('role:admin');
	Route::resource('generalrequests', 'GeneralrequestController', ['except' => ['index']])->middleware('role:admin');
	
	/*
	//Developer and debug routes:
	Route::get('developer/benches_sheets','Controller@benches_sheets')->middleware('role:admin,editor');
	*/

	/*
	//To redirect any other route to home:
	Route::any('{query}', function() { 
		return redirect('/'); 
	})->where('query', '.*');
	*/
});