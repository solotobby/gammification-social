<div>
    {{-- Stop trying to control. --}}
     <div class="content content-full content-boxed">
        <!-- Dynamic Table with Export Buttons -->
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">
                    Referral List
                </h3>
            </div>
            <div class="block-content block-content-full">


                <table class="table table-bordered table-striped table-vcenter">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($referralList as $list)
                            <tr>
                                <td>{{ $list->name }}</td>
                                
                                <td>{{ $list->created_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

</div>
