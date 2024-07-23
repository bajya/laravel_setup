<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
   <head>

      @include('layouts.frontend.head')
      
   </head>
   <body>
      <header>
        @include('layouts.frontend.header')
        
      </header>
      <main>
        @yield('content')
      </main>
       @include('layouts.frontend.footer')
      
        
        
      
      @include('layouts.frontend.script')
      @yield('script')
      
   </body>
</html>