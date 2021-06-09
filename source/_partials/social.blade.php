<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@tonysmdev">
<meta name="twitter:creator" content="@tonysmdev">
<meta name="twitter:title" content="{{ isset($title) && !empty($title) ? $title : "Tony Messias" }}">
<meta property="og:title" content="{{ isset($title) && !empty($title) ? $title : "Tony Messias" }}" />
<meta property="og:url" content="{{ $page->getUrl() }}" />
<meta property="og:description" content="{{ isset($description) && !empty($description) ? $description : "Yo! I'm a programmer from Brazil sharing my thoughts and experiments over here."}}" />
<meta name="twitter:description" content="{{ isset($description) && !empty($description) ? $description : "Yo! I'm a programmer from Brazil sharing my thoughts and experiments over here." }}">
@if (isset($image))
<meta property="og:image" content="{{ $image }}" />
<meta name="twitter:image" content="{{ $image }}">
@endif
