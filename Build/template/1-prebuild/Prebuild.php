&lt;?php

class Phake_Script_Prebuild extends Phake_Builder_Script
{
    
    function up()
    {
        phake('gen '.dirname(__FILE__).'/config-template/ config/');
    }
    
    function down()
    {
        
    }
    
}

?&gt;