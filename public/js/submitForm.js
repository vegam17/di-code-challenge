$( document ).ready( function (){

    // bind function to contact form submit
    $( '#contact-form' ).on( 'submit', function( e ){

        // prevent regular form submission
        e.preventDefault();

        // hides validation errors if prior submission displayed them
        clearValidationErrors();

        // clear alert from prior submission, remove alert type
        var alert = $( '#contact-form-alert' );
        alert.addClass( 'hidden' ).removeClass( 'alert-success alert-danger' );

        // gather form data
        var form = $( '#contact-form' ),
        data = form.serialize(),
        url = form.attr( 'action' );

        // send ajax post request
        $.post( {
            url,
            data,
            dataType: 'json',
            success: function( res ){

                // display correct alert and message
                var alertClass = res.success ? 'alert-success' : ( res.errors.length > 0 ? 'alert-danger' : 'alert-warning' );
                alert.html( res.data ).addClass( alertClass ).removeClass( 'hidden' );

                // if validation errors, display them
                if( 
                    !res.success && 
                    res.hasOwnProperty( 'errors' ) && 
                    res.errors.length > 0 
                ) displayValidationErrors( res.errors );
                
                if( !res.success ) return;

                // reset form if successful
                $( '#contact-form' )[0].reset();

                // hide confirmation notice after 5 seconds
                setTimeout( function() {
                    alert.addClass( 'hidden' );
                }, 5000 );
            }
        } )
    });

} );

/**
 * Adds error classes to invalid form fields
 *
 * @param {Array.<string>} errors List of form ids to add error classes to
 *
 * @return {null} 
 *     
 */
function displayValidationErrors( errors ){
    errors.map( function ( id ) {
        $( '#' + id ).parent().addClass( 'has-error' );
    } );
}

/**
 * Removes error classes from form fields
 *
 * @return {null} 
 *     
 */
function clearValidationErrors(){
    $( '.form-group' ).removeClass( 'has-error' );
}