<?php
$block_mtgdistribute_capabilities = array(

    'block/mtgdistribute:view' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'legacy' => array(
            'admin' => CAP_ALLOW
        )
    ),

    'block/mtgdistribute:distribute' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'admin' => CAP_ALLOW
        )
    )

);

?>
