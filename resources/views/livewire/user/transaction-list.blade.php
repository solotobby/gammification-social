<div>
    {{-- In work, do what you enjoy. --}}
    <div class="content content-full content-boxed">
        <!-- Dynamic Table with Export Buttons -->
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">
                    Transaction List
                </h3>
            </div>
            <div class="block-content block-content-full">


                <table class="table table-bordered table-striped table-vcenter">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Amount</th>
                            <th>Currency</th>
                            <th>Description</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($txList as $list)
                            <tr>
                                <td >{{ $list->ref }}</td>
                                <td >{{ $list->amount }}</td>
                                <td >{{ $list->currency }}</td>
                                <td >{{ $list->description }}</td>
                                <td >{{ $list->created_at }}</td>
                            </tr>

                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>




    </div>
