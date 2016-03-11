

/**
 * Function for asynchronously retrieving the
 * list of users, based on any search/filter
 * criteria, with pagination
 *
 * @param page
 * @param search
 * @param filter
 * @param filterOption
 * @param showLoader
 */
function getUsers(page, search, filter, filterOption, showLoader) {

    // set defaults
    page = typeof page !== 'undefined' ? page : 1;
    search = typeof search !== 'undefined' ? search : '';
    filter = typeof filter !== 'undefined' ? filter : '';
    filterOption = typeof filterOption !== 'undefined' ? filterOption : '';
    showLoader = typeof showLoader !== 'undefined' ? showLoader : true;

    // make ajax request to load users
    $.ajax({
        url: SITE_URL + '/admin',
        type: 'GET',
        dataType: 'json',
        data: { page : page, search : search, filter : filter, filterOption : filterOption }
    }).done(function(data) {

        $('.user-administration__users').html(data);


    }).fail(function () {

        alert('Users could not be loaded.');

    });

}

/**
 * Function to update a user given the url and formData
 *
 * @param url
 * @param formData
 */
function updateUser(url, formData) {

    // make ajax request to load users
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: formData,
        statusCode: {
            // determine status code and respond accordingly
            200:
            function () {
                // get user-administration container
                var container = $('.user-administration');

                // retrieve search and filter/filter option values from page elements
                // so pagination works within the current results set
                var search = container.find('.user-administration__user-search').val();
                var filter = container.find('.user-administration__user-filter').val();
                var filterOption = container.find('.user-administration__user-filter-option').val();
                var page = container.find('.pagination .active span').html();

                // get users based on page and any existing search or filter and filter option
                getUsers(page, search, filter, filterOption, false);
            },
            499:
                function (data) {
                    var error = '';
                    // loop each error and add errors to error string
                    $.each(data.responseJSON.errors, function(index, value) {
                        error += value.title + '\n';
                    });

                    // show errors
                    alert(error);

                },

        }

    });

}


$(document).ready(function() {

    // bind on click to pagination links
    $('.user-administration').on('click', '.pagination a', function (e) {

        e.preventDefault();

        // get user-administration container
        var container = $(this).closest('.user-administration');

        // retrieve search and filter/filter option values from page elements
        // so pagination works within the current results set
        var search = container.find('.user-administration__user-search').val();
        var filter = container.find('.user-administration__user-filter').val();
        var filterOption = container.find('.user-administration__user-filter-option').val();

        // get users based on page and any existing search or filter and filter option
        getUsers($(this).attr('href').split('page=')[1], search, filter, filterOption);

    });


    // variable for storing ajax requests
    var requestTimeout;

    // bind keyup event to search input
    $('.user-administration').on('keyup', '.user-administration__user-search', function (e) {

        // get value of input
        var value = this.value;

        // if the length is 3 or more characters, or the user pressed ENTER, search
        if (this.value.length >= 3 || e.keyCode == 13) {

            // get user-administration container
            var container = $(this).closest('.user-administration');

            // retrieve filter/filter option values from page elements
            // so pagination works within the current results set
            var filter = container.find('.user-administration__user-filter').val();
            var filterOption = container.find('.user-administration__user-filer-option').val();

            // set delay amount
            // for user to stop typing
            var requestDelay = 200;

            /**
             * Throttle user requests so that we can wait until the user
             * has stopped typing before making ajax calls
             */

            // clear the previous timeout request
            clearTimeout(requestTimeout);

            // set new timeout request
            requestTimeout = setTimeout(function () {
                // get users based on this search (with filter values, if applicable)
                getUsers(1, value, filter, filterOption);

            }, requestDelay);

        }

     });


    // default state and access array values
    var stateValues = {'active' : 'Active', 'disabled' : 'Disabled'};
    var accessValues = {'basic' : 'Basic', 'admin' : 'Admin','superadmin' : 'Superadmin'};

    // bind on change event for filter drop down to populate filter options
    $('.user-administration').on('change', '.user-administration__user-filter', function (e) {

        // getuser-administration container
        var container = $(this).closest('.user-administration');

        // remove previous options, leaving initial option
        $('.user-administration__user-filter-option').children('option:not(:first)').remove();

        // set actions for each case
        switch (this.value) {

            case 'state':
                // set options for active/disabled (state) filter
                $.each(stateValues, function(key, value) {
                    container.find('.user-administration__user-filter-option').append($('<option/>', {
                        value : key,
                        text: value
                    }));
                });
                $('.user-administration__user-filter-option').prop('disabled', false);
                break;

            case 'server_role':
                // set options for access (server_role) filter
                $.each(accessValues, function(key, value) {
                    $('.user-administration__user-filter-option').append($('<option/>', {
                        value : key,
                        text: value
                    }));
                });

                $('.user-administration__user-filter-option').prop('disabled', false);
                break;

            default:
                // by default, disable filter options
                $('.user-administration__user-filter-option').prop('disabled', true);

                // check for search value
                var search = $('.user-administration__user-search').val();
                // reset table (with search value, if applicable)
                getUsers(1, search);
        }


    });


    // bind on change event for filters to user properties
    $('.user-administration').on('change', '.user-administration__user-filter-option', function (e) {

        // get user-administration container
        var container = $(this).closest('.user-administration');

        // retrieve search and filter/filter option values from page elements
        // so pagination works within the current results set
        var search = container.find('.user-administration__user-search').val();
        var filter = container.find('.user-administration__user-filter').val();

        // get users based on filter and filter option (with search value, if applicable)
        getUsers(1, search, filter, this.value);

    });


    // bind on click to reset table
    $('.user-administration').on('click', '.user-administration__user-reset', function (e) {

        e.preventDefault();

        // get user-administration container
        var container = $(this).closest('.user-administration');

        // remove search text
        container.find('.user-administration__user-search').val('');
        // set first filter option as selected
        container.find('.user-administration__user-filter').val(container.find('.user-administration__user-filter option:first').val());
        // remove previous filter options and disable
        container.find('.user-administration__user-filter-option').children('option:not(:first)').remove();

        container.find('.user-administration__user-filter-option').prop('disabled', true);

        // get users
        getUsers();

    });


    // bind on click to activate/disable (state) buttons
    $('.user-administration').on('submit', '.user-administration__table__state-form', function (e) {

        e.preventDefault();

        // retrieve form data
        var formData = $(this).serialize();
        url = SITE_URL + '/admin/update-user-state';

        updateUser(url, formData);

    });


    // bind on click to access (server_role) buttons
    $('.user-administration').on('submit', '.user-administration__table__server-role-form', function (e) {

        e.preventDefault();

        // retrieve form data
        var formData = $(this).serialize();
        url = SITE_URL + '/admin/update-user-server-role';

        updateUser(url, formData);

    });

    $body = $("body");

    $(document).on({
        ajaxStart: function() { $body.addClass("loading");},
        ajaxStop: function() { setTimeout(function(){$body.removeClass("loading"); }, 200); }
    });


});
