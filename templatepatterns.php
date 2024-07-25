<?php 
return [
    [
        "pattern" => '/\{\{\s*(\$.*?)\s*\}\}/',
        "replace" => "<?php echo $1; ?>" 
    ],
    [
        "pattern" => '/\[b\](.*?)\[\/b\]/s',
        "replace" => "<b>$1</b>" 
    ],
    [
        "pattern" => '/@if\((.*)\)/',
        "replace" => "<?php if ($1) : ?>" 
    ],
    [
        "pattern" => '/@else/',
        "replace" => "<?php else : ?>" 
    ],
    [
        "pattern" => '/@endif/s',
        "replace" => "<?php endif; ?>" 
    ],
    [
        "pattern" => '/@isset\((.*)\)/',
        "replace" => "<?php if(isset($1)) : ?>" 
    ],
    [
        "pattern" => '/@empty/',
        "replace" => "<?php else : ?>" 
    ],
    [
        "pattern" => '/@foreach\((.*)\)/',
        "replace" => "<?php foreach($1) : ?>" 
    ],
    [
        "pattern" => '/@endforeach/s',
        "replace" => "<?php endforeach; ?>" 
    ],
];