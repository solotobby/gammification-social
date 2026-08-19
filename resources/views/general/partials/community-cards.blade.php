@foreach ($communities as $community)
  @include('general.partials.community-card', ['community' => $community])
@endforeach
