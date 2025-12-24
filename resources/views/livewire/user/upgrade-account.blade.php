<div>
    {{-- The best athlete wants his opponent at his best. --}}
    <main id="main-container">

        <!-- Hero -->
        <div class="bg-image" style="background-image: url('assets/media/photos/photo11@2x.jpg');">
            <div class="bg-black-75">
                <div class="content content-boxed text-center">
                    <div class="py-5">
                        <h1 class="fs-2 fw-normal text-white mb-2">
                            <i class="fa fa-arrow-up me-1"></i> Upgrade Account
                        </h1>
                        <h2 class="fs-4 fw-normal text-white-75">Go to Cretor or Influencer to monentize your account!
                        </h2>
                    </div>
                </div>
            </div>
        </div>
        <!-- END Hero -->

        <!-- Pricing Tables -->
        <div class="content content-boxed overflow-hidden">

            @if (session()->has('success'))
                <div class="alert alert-success mb-2" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="row py-5">

                @foreach ($levels as $level)
                    <div class="col-md-6 col-xl-4">
                        @if ($activeLevel && $activeLevel->level_id == $level->id)
                            <!-- Freelancer Plan -->
                            <div class="block block-rounded block-themed text-center">
                                <div class="block-header bg-muted">
                                    <h3 class="block-title">{{ $level->name }}</h3>
                                </div>
                                <div class="block-content bg-body-light">
                                    <div class="py-2">
                                        <p class="h1 fw-bold mb-2">{{ getCurrencyCode() }}{{ convertToBaseCurrency($level->amount, auth()->user()->wallet->currency) }}</p>
                                        <p class="h6 text-muted">per month</p>
                                    </div>
                                </div>
                                <div class="block-content">
                                    <div class="py-2">
                                        <p>
                                            <strong>3</strong> Projects
                                        </p>
                                        <p>
                                            <strong>1GB</strong> Storage
                                        </p>
                                        <p>
                                            <strong>1</strong> Monthly Backup
                                        </p>
                                        <p>
                                            <strong>10</strong> Clients
                                        </p>
                                        <p>
                                            <strong>Email</strong> Support
                                        </p>
                                    </div>
                                </div>
                                <div class="block-content block-content-full bg-body-light">
                                    <span class="btn btn-hero btn-secondary disabled px-4">
                                        <i class="fa fa-check opacity-50 me-1"></i> Active Plan
                                    </span>
                                </div>
                            </div>
                            <!-- END Freelancer Plan -->
                        @else
                            <!-- Startup Plan -->

                            <div class="block block-link-pop block-rounded text-center"
                                wire:click="upgradeLevel('{{ $level->id }}')">
                                <div class="block-header">

                                    <h3 class="block-title">{{ $level->name }}</h3>
                                </div>
                                <div class="block-content bg-body-light">
                                    <div class="py-2">
                                        <p class="h1 fw-bold mb-2">
                                            {{ getCurrencyCode() }}{{ convertToBaseCurrency($level->amount, auth()->user()->wallet->currency) }}
                                        </p>
                                        <p class="h6 text-muted">per month</p>
                                    </div>
                                </div>
                                <div class="block-content">
                                    <div class="py-2">
                                        <p>
                                            <strong>10</strong> Projects
                                        </p>
                                        <p>
                                            <strong>30GB</strong> Storage
                                        </p>
                                        <p>
                                            <strong>30</strong> Monthly Backups
                                        </p>
                                        <p>
                                            <strong>500</strong> Clients
                                        </p>
                                        <p>
                                            <strong>FULL</strong> Support
                                        </p>
                                    </div>
                                </div>
                                <div class="block-content block-content-full bg-body-light">
                                    <span class="btn btn-hero btn-primary px-4">
                                        <i class="fa fa-arrow-up opacity-50 me-1"></i> Upgrade
                                    </span>
                                </div>
                            </div>
                            <!-- END Startup Plan -->
                        @endif

                    </div>
                @endforeach





            </div>
        </div>
        <!-- END Pricing Tables -->
    </main>
</div>
