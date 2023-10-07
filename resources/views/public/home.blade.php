@extends('public.layout')
@section('title', 'Approval Adjustment')

@section('content')
    <!-- Hero section -->
    <section class="hero-section">
        <div class="hero-slider owl-carousel">
            @foreach ($listNews as $rowNews)
                <a href="#">
                    <div class="hs-item set-bg" data-setbg="{{ $rowNews['url_image'] }}">
                        {{-- <div class="hs-text">
                        <div class="container">
                            <h2>The Best <span>Games</span> Out There</h2>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec malesuada <br> lorem maximus
                                mauris scelerisque, at rutrum nulla dictum. Ut ac ligula sapien. <br>Suspendisse cursus
                                faucibus finibus.</p>
                            <a href="#" class="site-btn">Read More</a>
                        </div>
                    </div> --}}
                    </div>
                </a>
            @endforeach
        </div>
    </section>
    <!-- Hero section end -->

    <!-- Latest news section -->
    <div class="latest-news-section">
        <div class="ln-title">Latest News</div>
        <div class="news-ticker">
            <div class="news-ticker-contant">
                <div class="nt-item"><span class="new">new</span>Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                </div>
                <div class="nt-item"><span class="strategy">strategy</span>Isum dolor sit amet, consectetur adipiscing elit.
                </div>
                <div class="nt-item"><span class="racing">racing</span>Isum dolor sit amet, consectetur adipiscing elit.
                </div>
            </div>
        </div>
    </div>
    <!-- Latest news section end -->

    <!-- Feature section -->
    <section class="feature-section spad">
        <div class="container">
            <div class="row">
                @foreach ($listNews as $rowNews)
                    <div class="col-lg-3 col-md-6 p-0">
                        <div class="feature-item set-bg" data-setbg="{{ $rowNews['url_image'] }}">
                            <span class="cata new">{{ $rowNews['category_name'] }}</span>
                            <div class="fi-content text-white">
                                <h5><a href="#">{{ $rowNews['title'] }}</a></h5>
                                {{-- <p>{!! substr($rowNews['content'], 0, 50) !!}.... </p> --}}
                                <a href="#" class="fi-comment">{{ $rowNews['diffCreatedAt'] }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- Feature section end -->

    <!-- Recent game section  -->
    <section class="recent-game-section spad set-bg" data-setbg="{{ asset('gameWarrior/img/recent-game-bg.png') }}">
        <div class="container">
            <div class="section-title">
                <div class="cata adventure">Videos</div>
                <h2>Recent Videos</h2>
            </div>
            <div class="row">
                @foreach ($listVideos as $rowVideo)
                    <div class="col-lg-4 col-md-6">
                        <div class="recent-game-item">
                            <div class="rgi-content">
                                <div class="cata adventure" style="margin-bottom:5px;">
                                    {{ $rowVideo['category_name'] }}
                                </div>
                                <iframe width="300" height="204" src="{{ $rowVideo['youtube_embed'] }}"></iframe>

                                <h5>{{ $rowVideo['title'] }}</h5>
                                <p>
                                    {!! substr($rowVideo['content'], 0, 100) !!}....
                                </p>
                                {{-- <a href="#" class="comment">3 Comments</a> --}}
                                {{-- <div class="rgi-extra">
                                    <div class="rgi-star"><img src="{{ asset('gameWarrior/img/icons/star.png') }}"
                                            alt=""></div>
                                    <div class="rgi-heart"><img src="{{ asset('gameWarrior/img/icons/heart.png') }}"
                                            alt=""></div>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- Recent game section end -->

    <!-- Tournaments section -->
    <section class="tournaments-section spad">
        <div class="container">
            <div class="tournament-title">Tournaments</div>
            <div class="row">
                <div class="col-md-6">
                    <div class="tournament-item mb-4 mb-lg-0">
                        <div class="ti-notic">Premium Tournament</div>
                        <div class="ti-content">
                            <div class="ti-thumb set-bg" data-setbg="{{ asset('gameWarrior/img/tournament/1.jpg') }}">
                            </div>
                            <div class="ti-text">
                                <h4>World Of WarCraft</h4>
                                <ul>
                                    <li><span>Tournament Beggins:</span> June 20, 2018</li>
                                    <li><span>Tounament Ends:</span> July 01, 2018</li>
                                    <li><span>Participants:</span> 10 teams</li>
                                    <li><span>Tournament Author:</span> Admin</li>
                                </ul>
                                <p><span>Prizes:</span> 1st place $2000, 2nd place: $1000, 3rd place: $500</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="tournament-item">
                        <div class="ti-notic">Premium Tournament</div>
                        <div class="ti-content">
                            <div class="ti-thumb set-bg" data-setbg="{{ asset('gameWarrior/img/tournament/2.jpg') }}">
                            </div>
                            <div class="ti-text">
                                <h4>DOOM</h4>
                                <ul>
                                    <li><span>Tournament Beggins:</span> June 20, 2018</li>
                                    <li><span>Tounament Ends:</span> July 01, 2018</li>
                                    <li><span>Participants:</span> 10 teams</li>
                                    <li><span>Tournament Author:</span> Admin</li>
                                </ul>
                                <p><span>Prizes:</span> 1st place $2000, 2nd place: $1000, 3rd place: $500</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Tournaments section bg -->
@endsection
