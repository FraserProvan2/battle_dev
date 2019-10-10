
require('./bootstrap');

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

/**
 * Battle
 */

require('./components/battle/BattleAlpha.jsx'); // parent

/**
 * Profile Card
 */

require('./components/profile/Profile.jsx'); // parent

/**
 * Finder
 */

require('./components/finder/Finder.jsx'); // parent
