<div>
    <p>{{__('Hi')}}</p>
    <p>
        {{__('You have been invited to join the activities of')}} {{ auth()->user()->name }}.
        <a href="{{env('APP_URL')}}/register/?token={{$token}}">{{__('Click here to join')}}</a>
    </p>
    <p>
        {{__('Thank you')}}
    </p>
</div>
