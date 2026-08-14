{{--
    Skeleton: Data Table
    Usage: <x-skeleton.table :rows="6" :cols="8" />
    Props:
      $rows = number of skeleton rows  (default 6)
      $cols = number of columns        (default 8)
      $searchBar = show search bar above table (default true)
      $pagination = show pagination below (default true)
--}}
@props(['rows' => 6, 'cols' => 8, 'searchBar' => true, 'pagination' => true])

<div role="status" aria-label="Loading table data" aria-busy="true">

    <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: .75rem;">
        
        {{-- Card Header: Filter & Search bar --}}
        @if($searchBar)
        <div class="card-header bg-white py-3 d-flex flex-wrap gap-2 align-items-center border-bottom">
            <div class="sk sk-md" style="width:220px;max-width:250px;border-radius:6px;height:32px;"></div>
            <div class="sk sk-md" style="width:130px;max-width:150px;border-radius:6px;height:32px;"></div>
            <div class="sk sk-md ms-auto" style="width:110px;border-radius:6px;height:32px;"></div>
        </div>
        @endif

        {{-- Table Body --}}
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width:110px;"><div class="sk sk-xs" style="width:70px;"></div></th>
                            <th><div class="sk sk-xs" style="width:90px;"></div></th>
                            <th><div class="sk sk-xs" style="width:80px;"></div></th>
                            <th><div class="sk sk-xs" style="width:55px;"></div></th>
                            <th><div class="sk sk-xs" style="width:75px;"></div></th>
                            <th><div class="sk sk-xs" style="width:50px;"></div></th>
                            <th><div class="sk sk-xs" style="width:65px;"></div></th>
                            <th class="pe-3 text-end" style="width:90px;"><div class="sk sk-xs ms-auto" style="width:50px;"></div></th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($r = 0; $r < $rows; $r++)
                        <tr>
                            <td class="ps-3"><div class="sk sk-sm" style="width:85px;height:22px;border-radius:4px;"></div></td>
                            <td>
                                <div class="sk sk-sm mb-1" style="width:140px;"></div>
                                <div class="sk sk-xs" style="width:100px;"></div>
                            </td>
                            <td>
                                <div class="sk sk-sm mb-1" style="width:90px;"></div>
                                <div class="sk sk-xs" style="width:40px;"></div>
                            </td>
                            <td><div class="sk sk-sm" style="width:50px;"></div></td>
                            <td><div class="sk sk-sm" style="width:35px;"></div></td>
                            <td><div class="sk sk-sm sk-pill" style="width:65px;height:20px;"></div></td>
                            <td><div class="sk sk-sm" style="width:80px;"></div></td>
                            <td class="pe-3 text-end">
                                <div class="sk sk-sm ms-auto" style="width:36px;height:28px;border-radius:4px;"></div>
                            </td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Card Footer: Pagination --}}
        @if($pagination)
        <div class="card-footer bg-white py-2.5 d-flex justify-content-between align-items-center border-top">
            <div class="sk sk-xs" style="width:130px;"></div>
            <div class="d-flex gap-1">
                @for ($p = 0; $p < 4; $p++)
                    <div class="sk sk-circle" style="width:28px;height:28px;"></div>
                @endfor
            </div>
        </div>
        @endif

    </div>

</div>
