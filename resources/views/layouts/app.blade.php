<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=Nunito:400,600,700" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Nunito', sans-serif;
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: .5px;
        }
        main {
            min-height: 90vh;
        }
    </style>
</head>

<body>
<div id="app">

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">

            <a class="navbar-brand text-primary" href="{{ url('/') }}">
                <i class="bi bi-ticket-perforated-fill me-1"></i>
                {{ config('app.name', 'Laravel') }}
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">

                <!-- LEFT -->
                <ul class="navbar-nav me-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('home') }}">
                                <i class="bi bi-house-door"></i> Member Dashboard
                            </a>
                        </li>
 

                    @endauth
                    
                </ul>

                <!-- RIGHT -->
                <ul class="navbar-nav ms-auto align-items-center">

                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Register</a>
                        </li>
                    @else

                        <!-- USER DROPDOWN -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center"
                               href="#"
                               data-bs-toggle="dropdown">

                                <i class="bi bi-person-circle fs-5 me-1"></i>
                                {{ Auth::user()->name }}

                            </a>

                            <div class="dropdown-menu dropdown-menu-end shadow-sm">

                                <span class="dropdown-item-text text-muted small">
                                    Logged in as<br>
                                    <strong>{{ Auth::user()->email }}</strong>
                                </span>

                                <div class="dropdown-divider"></div>

                                <a class="dropdown-item" href="{{ url('home') }}">
                                    <i class="bi bi-speedometer2 me-1"></i> Dashboard
                                </a>

                                <a class="dropdown-item text-danger"
                                   href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>

                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="container py-4">
            <!-- Alert Container -->
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="text-center text-muted small py-3">
        © {{ date('Y') }} {{ config('app.name') }} — All rights reserved
    </footer>

</div>
<script>


//---------------------------
// alert script  
function showAlert(type, message) {
    const container = document.getElementById('alert-container');
    if (!container) return;

    // Clear existing alerts
    container.innerHTML = '';

    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    alertDiv.setAttribute('role', 'alert');

    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    container.appendChild(alertDiv);

    // 🔥 Scroll to top smoothly
    window.scrollTo({ top: 0, behavior: 'smooth' });

    // 🔥 Auto hide after 2 seconds
    setTimeout(() => {
        alertDiv.classList.remove('show');
        alertDiv.classList.add('fade');

        setTimeout(() => {
            alertDiv.remove();
        }, 300); // wait for fade animation
    }, 4000);
}

//---------------------------
document.addEventListener('DOMContentLoaded', function() {

    // Checklist functionality
    const container = document.getElementById('checklist-container');
    const addBtn = document.getElementById('add-checklist');

    if (addBtn && container) {
        addBtn.addEventListener('click', function() {
            const div = document.createElement('div');
            div.classList.add('input-group', 'mb-2', 'checklist-item');

            div.innerHTML = `
                <input type="text" name="checklist[]" class="form-control" placeholder="Checklist item" required>
                <button type="button" class="btn btn-danger remove-checklist">X</button>
            `;

            container.appendChild(div);
        });

        container.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-checklist')) {
                e.target.closest('.checklist-item').remove();
            }
        });
    }

    // Drag and Drop functionality
    document.querySelectorAll('.ticket-cards-container').forEach(container => {
        new Sortable(container, {
            group: 'tickets',
            animation: 150,
            draggable: '.ticket-card',
            handle: '.card',

            onMove: function (evt) {
                const fromStatus = evt.from.closest('.ticket-column').dataset.status;

                // 🚫 If ticket is from CLOSED, block move
                if (fromStatus === 'closed') {
                    return false;
                }
            },

            onEnd: function(evt) {
                const ticketId = evt.item.dataset.ticketId;
                const newStatus = evt.to.closest('.ticket-column').dataset.status;
                const oldStatus = evt.from.closest('.ticket-column').dataset.status;

                console.log(`Moving ticket ${ticketId} from ${oldStatus} to ${newStatus}`);

                // extra safety
                if (oldStatus === 'closed') {
                    alert('Cannot move tickets from Closed column');
                    evt.from.appendChild(evt.item); // Revert
                    return;
                }

                // Determine which endpoint to call based on new status
                let endpoint = '';
                let methodName = '';
                
                switch(newStatus) {
                    case 'in_progress':
                        endpoint = `/ticket/${ticketId}/start`; // Changed
                        methodName = 'start';
                        break;
                    case 'open':
                        endpoint = `/ticket/${ticketId}/open`; // Changed
                        methodName = 'open';
                        break;
                    case 'closed':
                        endpoint = `/ticket/${ticketId}/closeTicket`; // Changed
                        methodName = 'close';
                        break;
                    default:
                        console.error('Unknown status:', newStatus);
                        evt.from.appendChild(evt.item);
                        return;
                }

                console.log(`Calling ${methodName}() method via ${endpoint}`);

                // Show loading state
                evt.item.style.opacity = '0.6';

                // Make API call

                fetch(endpoint, {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
})
.then(response => response.json())
.then(data => {
    evt.item.style.opacity = '1';

    if (data.success) {
        showAlert('success', data.message || 
            `Ticket moved to ${newStatus.replace('_', ' ')}`);
    } else {
        showAlert('error', data.message || 
            'Something went wrong');
        evt.from.appendChild(evt.item);
    }
})
.catch(error => {
    evt.item.style.opacity = '1';
    showAlert('error', 'Server error occurred');
    evt.from.appendChild(evt.item);
});

            }
        });
    });

    // Helper function to show toast messages
    function showToast(message, type = 'info') {
        // Check if toast container exists
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            document.body.appendChild(toastContainer);
        }

        const toastId = 'toast-' + Date.now();
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-bg-${type} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        
        const toastEl = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();
        
        // Remove toast from DOM after it's hidden
        toastEl.addEventListener('hidden.bs.toast', function () {
            toastEl.remove();
        });
    }

    // Test function for debugging
    window.testDrag = function() {
        console.log('=== Testing Drag ===');
        const ticket = document.querySelector('.ticket-card');
        if (!ticket) {
            alert('No tickets found');
            return;
        }
        
        const ticketId = ticket.dataset.ticketId;
        console.log('Testing with ticket ID:', ticketId);
        
        // Test the start endpoint with new URL
        fetch(`/ticket/${ticketId}/start`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            console.log('Test result:', data);
            alert('Test successful! Check console.');
        })
        .catch(err => {
            console.error('Test error:', err);
            alert('Test failed: ' + err.message);
        });
    };

    // Notification modal functionality
const notificationModal = document.getElementById('notificationsModal');

if (notificationModal) {
    notificationModal.addEventListener('shown.bs.modal', function () {
        fetch('{{ route("notifications.mark-read") }}', { 
            method: 'POST', 
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(() => {
            const badge = document.querySelector('#notificationBtn .badge');
            if (badge) badge.remove();
        });
    });
}


    console.log('Ticket drag and drop initialized');
});
</script>
</body>
</html>
