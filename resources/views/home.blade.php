@extends('layouts.app')

@section('content')



<div class="container-fluid py-4">

    <div id="alert-container"></div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    <!-- error Message -->
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Notifications Bell -->
    <div class="d-flex justify-content-end mb-3 px-3">
        <button id="notificationBtn" type="button" class="btn btn-light position-relative" data-bs-toggle="modal" data-bs-target="#notificationsModal" aria-label="View notifications">
            <i class="bi bi-bell fs-4"></i>
            @if($allNotifications->count() > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ $unread->count() }}
                    <span class="visually-hidden">All Notifications</span>
                </span>
            @else
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary">0</span>
            @endif
        </button>
    </div>

    <!-- allNotifications Modal -->
    <div class="modal fade" id="notificationsModal" tabindex="-1" aria-labelledby="notificationsModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="notificationsModalLabel">Notifications</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            @if($allNotifications->isEmpty())
              <p class="text-center text-muted mb-0">No Notifications.</p>
            @else
              <ul class="list-group">
                @foreach($allNotifications as $notification)
                  <li class="list-group-item d-flex flex-column">
                    <strong>{{ $notification->data['title'] ?? 'Notification' }}</strong>
                    <small class="mb-2">{{ $notification->data['message'] ?? '' }}</small>
                    <small class="text-muted fst-italic" style="font-size: 0.8rem;">{{ $notification->created_at->diffForHumans() }}</small>
                  </li>
                @endforeach
              </ul>
            @endif
          </div>
        </div>

      </div>
    </div>
        
<div class="row">
     @role('admin')
             <div class="mb-4 col-md-2 px-1">
                    <button data-bs-toggle="modal" data-bs-target="#addBoard" class="btn btn-primary">
                ➕ Add Board
            </button>
    </div>
            @include('partials.add_board_modal')

        <div class="mb-4 col-md-2 px-1">
                    <button data-bs-toggle="modal" data-bs-target="#addTicket" class="btn btn-primary">
                ➕ Add Ticket
            </button>
    </div>
            @include('partials.add-ticket-modal')   
            <div class="mb-4 col-md-2 px-1">
                   <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#open-modal">Time Log</button>
            </div>
             @include('partials.timelog_modal')
                        

       @endrole     

            <div class="dropdown mb-4 col-md-2 px-1">
         <button class="btn btn-primary dropdown-toggle" type="button" id="boardDropdown" data-bs-toggle="dropdown" aria-expanded="false">
         Select Board
           </button>
          <ul class="dropdown-menu" aria-labelledby="boardDropdown">
           @foreach($boards as $board)
               <li>
                 <a class="dropdown-item" href="/board/{{$board->id}}">
                  {{ $board->name }}
              </a>
               </li>
          @endforeach
           </ul>
       </div>
       </div>
     @role('admin') 
    <div class="card mb-4 shadow-sm px-3">
        <div class="card-header bg-secondary text-white fw-bold">Top Points Users</div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Points</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->points }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
@endrole

@role('member')
    
@endrole
<div class="row">
@foreach($boards as $board)


<div class="col-md-5">

        <div class="card">
            <a href="board/{{$board->id}}"><div class="card-header"><h5>{{$board->name}}</h5></div>
            <div class="card-Body">
                <div>
                    <p>
                          {{$board->description}}</p>
                </div>
            </div></a>
        </div>
</div>

@endforeach

</div>
    <!-- TRELLO-STYLE BOARD -->

</div>


            </form>

                <!-- <input type="hidden" id="modal-user-id" value="">
                <p><strong>User:</strong> <span id="modal-user-name">--</span></p>
                <p><strong>Total Time:</strong> <span id="modal-user-time">--</span></p> -->
            </div>



        </div>
    </div>
</div>



@endsection
