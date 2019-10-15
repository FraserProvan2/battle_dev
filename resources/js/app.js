
require('./bootstrap');

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

/**
 * Battle
 */

require('./components/battle/BattleAlpha.jsx');
require('./components/battle/Finder.jsx');

/**
 * Profile Card
 */

require('./components/profile/Profile.jsx'); // parent

/**
 * Finder
 */

