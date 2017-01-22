;( function($, _, undefined){
    "use strict";

    ips.controller.register('discord.admin.settings.handshake', {

        initialize: function () {
            this.on( 'click', this.doHandshake );
        },

        /**
         * Event handler for doing a handshake with discord.
         *
         * @param	{event} 	e 		Event object
         * @returns {void}
         */
        doHandshake: function ( e ) {
            e.preventDefault();

            var botToken = $( e.currentTarget ).attr( 'data-token' );

            var socket = new WebSocket( "wss://gateway.discord.gg/?encoding=json&v=6" );

            socket.onerror = function( error ) { Debug.log( error ); };
            socket.onmessage = function( message )
            {
                try
                {
                    var data = JSON.parse( message.data );

                    if ( data.op === 10 )
                    {
                        socket.send(JSON.stringify({
                            op: 2,
                            d: {
                                token: botToken,
                                properties: {
                                    $browser: "DiscordBot (ahmadel, v1)"
                                },
                                large_threshold: 50
                            }
                        }));
                        socket.close();
                    }
                }
                catch ( error )
                {
                    Debug.log( error );
                }
            };
        }
    });
}(jQuery, _));