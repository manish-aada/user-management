<!DOCTYPE html>
<html lang="en">
   <head>
	  <meta charset="UTF-8">
	  <meta name="viewport" content="width=device-width, initial-scale=1.0">
	  <meta name="csrf-token" content="{{ csrf_token() }}">
	  <title>@yield('title', 'Laravel App')</title>
	  <meta name="csrf-token" content="{{ csrf_token() }}">
	  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
	  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	</head>
   <body>
	  <main>
		 <div class="container my-5">
			@yield('content')
		 </div>
	  </main>
	  @stack('scripts')
   </body>
</html>