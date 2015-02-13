var http = require('http')

var auth = require('./routes/auth')
var activities = require('./routes/activities')
var activities_checkins = require('./routes/activities_checkins')
var activities_missed_connections = require('./routes/activities_missed_connections')
var activities_comments = require('./routes/activities_comments')
var activities_images = require('./routes/activities_images')
var activities_likes = require('./routes/activities_likes')
var activities_links = require('./routes/activities_links')
var awards = require('./routes/awards')
var bios = require('./routes/bios')
var educations = require('./routes/educations')

var routes = {}

routes.apply = function (server) {
    // Auth
    server.post('/login', auth.login);
    server.post('/logout', auth.logout);
    server.post('/global_logout', auth.global_logout);
    server.post('/forgot_password', auth.generate_reset_token);
    server.post('/reset_password', auth.reset_password);
    server.get('/check_token', auth.check_token);

    // Activity

    server.get('/activities/comments/:activity_id', activities_comments.read_all);
    server.post('/activities/comments', activities_comments.create);
    server.del('/activities/comments/:activity_comment_id', activities_comments.delete);
    server.post('/activities/comments/report/:activity_comment_id', activities_comments.report);

    server.get('/activities/checkins/recent', activities_checkins.recent);
    server.get('/activities/checkins/:activity_id', activities_checkins.read);
    server.post('/activities/checkins', activities_checkins.create);

    server.get('/activities/links/:activity_id', activities_links.read);
    server.post('/activities/links', activities_links.create);

    server.get('/activities/missed_connections/recent', activities_missed_connections.recent);

    server.get('/activities/likes/:activity_id', activities_likes.read_all);
    server.post('/activities/likes', activities_likes.create);

    server.post('/activities/image', activities_images.create);

    server.get('/activity/sharedCount/:activity_id', activities.get_shared_count);
    server.post('/activities/share', activities.share);
    server.get('/activities/:type/:page', activities.read_by_type);
    server.post('/activities/:type/:page', activities.search_by_type);

    server.post('/radar/:type/:page', activities.read_for_radar);

    server.get('/activity/:activity_id', activities.read);
    server.get('/activities', activities.read_all);
    server.post('/activities', activities.create);
    server.del('/activities/:activity_id', activities.delete);

    server.get('/activity/sharedCount/:activity_id', activities.get_shared_count);
    server.post('/activities/share', activities.share);
    server.get('/activities/:type/:page', activities.read_by_type);
    server.get('/activities/user/:user_id/:page', activities.read_by_user);
    server.post('/activities/:type/:page', activities.search_by_type);

    server.get('/activity/:activity_id', activities.read);
    server.get('/activities', activities.read_all);
    server.post('/activities', activities.create);

    // Awards
    server.post('/awards', awards.create);
    server.put('/award/:user_awards_id', awards.update);
    server.get('/award/:user_awards_id', awards.read);
    server.get('/awards/', awards.read_all);
    server.get('/awards/user/:user_id', awards.user);
    server.del('/award/:user_awards_id', awards.delete);

    // Bios
    server.put('/bio', bios.update);
    server.get('/bio/', bios.read);
    server.get('/bio/:user_id', bios.userBio);

    // Educations

    server.post('/educations', educations.create);
    server.get('/educations/majors', educations.majors);
    server.get('/educations/majors/:user_school_id', educations.schoolMajors);
    server.get('/educations/minors/:user_school_id', educations.schoolMinors);
		server.get('/educations/campuses', educations.campuses);
    server.get('/educations/current', educations.current);
    server.get('/educations/current/:user_id', educations.current);
    server.get('/educations/year_names', educations.year_names);
    server.get('/educations', educations.read_all);
		server.get('/educations/user/:user_id', educations.user);
    server.put('/educations/:user_school_id', educations.update);
    server.get('/educations/:user_school_id', educations.read);
    server.del('/educations/:user_school_id', educations.delete);

    server.del('/educations/major/:user_school_major_id', educations.deleteMajor);
    server.del('/educations/minor/:user_school_minor_id', educations.deleteMinor);

    server.post('/educations/courses', courses.create);
    server.put('/educations/courses/:user_school_course_id', courses.update);
    server.get('/educations/course/:user_school_course_id', courses.read);
    server.get('/educations/course/:course_id/users/:page', courses.users);
    server.get('/educations/courses', courses.read_all);
    server.get('/educations/courses/university/:university_id/:searchStr', courses.read_all_by_university);
    server.del('/educations/course/:user_school_course_id', courses.delete);

    server.get('/educations/degree_types/:degree_type_id', degree_types.read);
    // server.get('/educations/highschools', );
    server.get('/educations/degree_types', degree_types.read_all);

    // Other
    server.get('/crossdomain.xml', utils.crossdomain_xml);
    server.get('/', utils.default_home);


}

module.exports = routes
