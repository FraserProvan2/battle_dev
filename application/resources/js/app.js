
require('./bootstrap');

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

/**
 * Battle
 */

require('./components/battle/Battle.jsx'); // parent
require('./components/battle/_Finder.jsx');
require('./components/battle/_Game.jsx');

/**
 * Profile Card
 */

require('./components/profile/Profile.jsx'); // parent
