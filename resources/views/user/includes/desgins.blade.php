@php
    $lang = app()->getLocale();
@endphp


<div class="row justify-content-center">

    <div class="col-12">
        <div class="portfolio-list-box">
            <div class="portfoliolist" id="portfoliolist">

                @foreach ($Desgins as $item)
                <div class="portfolio web-design" data-cat=".web-design">
                    <div class="portfolio-wrapper">
                        <div class="portfolio-img back-img" style="background-image: url('{{ $item->file }}?{{ rand() }}')"></div>
                        <div class="portfolio-wrapper-text">
                            <h3 class="h3-title"> {{ $item->{$lang.'_name'} }} </h3>
                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>
</div>
