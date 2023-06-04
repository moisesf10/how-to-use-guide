<ul class="sidebar-nav" id="sidebar-nav">

    <li class="nav-item">
        <a class="nav-link collapsed" href="{{route('admin_index')}}">
            <i class="bi bi-grid"></i>
            <span>Workspaces</span>
        </a>
    </li><!-- End Dashboard Nav -->

    <li class="nav-item">
        <a class="nav-link collapsed" href="{{route('admin_shared_workspace')}}">
            <i class="bi bi-share"></i>
            <span>Compartilhadas</span>
        </a>
    </li>

    @if(auth()->user()->indicates_system_admin)
    <li class="nav-heading">Administração Sistema</li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="{{route('admin_list_general_setting')}}">
            <i class="bi bi-gear"></i>
            <span>Geral</span>
        </a>
    </li><!-- End Profile Page Nav -->

    @endif


</ul>
