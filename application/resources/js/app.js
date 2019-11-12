
require('./bootstrap');

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

/**
 * Battle
 */

require('./components/battle/App.jsx'); // parent

/**
 * Profile Card
 */

require('./components/profile/App.jsx'); // parent
