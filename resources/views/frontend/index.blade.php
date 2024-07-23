@extends('layouts.frontend.app')

@section('content')
  
   <main>
        <section class="main-banner">
          <div class="container">
            <div class="row align-items-center">
             <div class="col-lg-6 col-md-6">
                <div class="banner-content">
                   <h1>Maintain your car like our baby</h1>
                   <p>App ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                   <div class="banner-holder">
                      <a href="#">
                      <img src="{{ asset('img/1.png') }}" alt="image">
                      </a>
                      <a href="#">
                      <img src="{{ asset('img/2.png') }}" alt="image">
                      </a>
                   </div>
                </div>
             </div>
             <div class="col-lg-6  col-md-6">
                <div class="banner-image">
                   <img src="{{ asset('img/main-banner.png') }}" alt="image">
                </div>
             </div>
          </div>
          </div>
        </section>
        <section class="terms-sec bg-gray space-cls">
          <div class="container">
            <div class="row">
              <div class="col-md-3 col-6">
                <div class="term-box">
                  <div class="icon">
                    <i class="fa fa-users"></i>
                  </div>
                  <div class="term-cont">
                    <h2 class="timer count-title count-number" data-to="1020" data-speed="2000"></h2>
                    <p>Users</p>
                  </div>
                </div>
              </div>
              <div class="col-md-3 col-6">
                <div class="term-box">
                  <div class="icon">
                    <i class="fa fa-heart"></i>
                  </div>
                  <div class="term-cont">
                    
                   <h2 class="timer count-title count-number" data-to="5679" data-speed="1500"></h2>
                    <p>Happy Clients</p>
                  </div>
                </div>
              </div>
              <div class="col-md-3 col-6">
                <div class="term-box">
                  <div class="icon">
                    <i class="fa fa-star"></i>
                  </div>
                  <div class="term-cont">
                     <h2 class="timer count-title count-number" data-to="2660" data-speed="1500"></h2>
                    <p>Reviews</p>
                  </div>
                </div>
              </div>
              <div class="col-md-3 col-6">
                <div class="term-box">
                  <div class="icon">
                    <i class="fa fa-download"></i>
                  </div>
                  <div class="term-cont">
                     <h2 class="timer count-title count-number" data-to="6789" data-speed="1500"></h2>
                    <p>App Downloads</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="sectionScroller" id="aboutUS"></div>
        </section>
        <section class="about-sec space-cls">
          <div class="container">
            <div class="section-title">
              <h2>About Our App</h2>
              <div class="bar"></div>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incidiunt labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida.</p>
            </div>
            <div class="row align-items-center">
               <div class="col-lg-6 col-md-6">
                  <div class="about-content">
                     <h3>We provide car wash service for Interior and Exterior, 24/7</h3>
                     <div class="bar"></div>
                     <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled.</p>
                     <p>If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet.</p>
                     <div class="banner-holder">
                      <a href="#">
                      <img src="{{ asset('img/1.png') }}" alt="image">
                      </a>
                      <a href="#">
                      <img src="{{ asset('img/2.png') }}" alt="image">
                      </a>
                     </div>
                  </div>
               </div>
               <div class="col-lg-6 col-md-6">
                  <div class="about-image">
                     <img src="{{ asset('img/about-img.png') }}" alt="image">
                  </div>
               </div>
            </div>  
          </div>
        </section>
        <section class="easycheck-sec bg-gray">
        	<div class="container">
        		<div class="checkin-sec space-cls">
	        		<div class="section-title">
		              <h2>Easy Subscription</h2>
		              <div class="bar"></div>
		              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incidiunt labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida.</p>
		            </div>
	        		<div class="row">
	        			<div class="col">
	        				<div class="easycheck-track">
	        					<div class="check-img">
	        						1
	        					</div>
	        					<div class="check-cont">
	        						<h4>Enter Phone number</h4>
	        					</div>
	        				</div>
	        			</div>
	        			<div class="col">
	        				<div class="easycheck-track">
	        					<div class="check-img">
	        						2
	        					</div>
	        					<div class="check-cont">
	        						<h4>Select subscription</h4>
	        					</div>
	        				</div>
	        			</div>
	        			<div class="col">
	        				<div class="easycheck-track">
	        					<div class="check-img">
	        						3
	        					</div>
	        					<div class="check-cont">
	        						<h4>Enter Car number</h4>
	        					</div>
	        				</div>
	        			</div>
                  <div class="col">
                     <div class="easycheck-track">
                        <div class="check-img">
                           4
                        </div>
                        <div class="check-cont">
                           <h4>Select Date & Time </h4>
                        </div>
                     </div>
                  </div>
                  <div class="col">
                     <div class="easycheck-track">
                        <div class="check-img">
                           5
                        </div> 
                        <div class="check-cont">
                           <h4>Make Payment</h4>
                        </div>
                     </div>
                  </div>
	        		</div>
	        	</div>
        	</div>
        </section>
        <section class="locker-sec space-cls">
          <div class="container">
            <div class="row align-items-center">
               <div class="col-lg-6 col-md-6">
                  <div class="about-image">
                     <img src="{{ asset('img/locker.png') }}" alt="image">
                  </div>
               </div>
               <div class="col-lg-6 col-md-6">
                  <div class="about-content">
                     <h3>How to access subscriptions</h3>
                     <div class="bar"></div>
                     <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</p>
                     <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</p>
                     
                     <div class="banner-holder">
                      <a href="#">
                      <img src="{{ asset('img/1.png') }}" alt="image">
                      </a>
                      <a href="#">
                      <img src="{{ asset('img/2.png') }}" alt="image">
                      </a>
                     </div>
                  </div>
               </div>
            </div>  
          </div>
        </section>
        <section class="overall-sec bg-gray space-cls">
          <div class="container">
            <div class="row align-items-center">
               <div class="col-md-6">
                  <div class="about-content">
                     <h3>Overall 400k+ Over User Please Get Download Now</h3>
                     <div class="bar"></div>
                     <p>If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet.</p>
                     <div class="banner-holder">
                      <a href="#">
                      <img src="{{ asset('img/1.png') }}" alt="image">
                      </a>
                      <a href="#">
                      <img src="{{ asset('img/2.png') }}" alt="image">
                      </a>
                     </div>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="about-image">
                     <img src="{{ asset('img/pedalocker.png') }}" alt="image">
                  </div>
               </div>
            </div>
          </div>
        </section>
        <section class="testimonial-slider space-cls">
          <div class="container">
            <div class="section-title">
              <h2>Testimonial</h2>
              <div class="bar"></div>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incidiunt labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida.</p>
            </div>
            <div class="testimonial-slider owl-carousel">
              <div class="item">
                <div class="testi-inner">
                  <div class="single-feedback">
                    <div class="icon">
                      <i class="fa fa-quote-left"></i>
                    </div>
                    <p>“Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna. Quis ipsum suspendisse ultrices gravida.”</p>
                    <!-- <div class="img-fill">
                      <img src="img/1.jpg" alt="client">
                    </div> -->
                    <div class="title">
                      <h3>Steven Smith</h3>
                      <span>CEO at EnvyTheme</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="item">
                <div class="testi-inner">
                  <div class="single-feedback">
                    <div class="icon">
                      <i class="fa fa-quote-left"></i>
                    </div>
                    <p>“Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna. Quis ipsum suspendisse ultrices gravida.”</p>
                    <!-- <div class="img-fill">
                      <img src="img/1.jpg" alt="client">
                    </div> -->
                    <div class="title">
                      <h3>Steven Smith</h3>
                      <span>CEO at EnvyTheme</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        
        <section class="subscribe-sec space-cls" style="background-image: url(img/app-download.png);">
          <div class="container">
            <div class="row align-items-center">
              <div class="col-md-6">
                <div class="subs-cont">
                  <h2>Subscribe For Our Newsletter</h2>
                </div>
              </div>
              <div class="col-md-6">
                <div class="subs-form">
                  <div class="form-group">
                    <div class="input_group">
                      <input type="text" name="" placeholder="Enter Your Email" class="form-control">
                      <a href="#" class="btn default-btn">Subscribe Now</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </main>
 
 @endsection 