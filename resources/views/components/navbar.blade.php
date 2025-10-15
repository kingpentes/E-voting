<nav class="bg-blue-600 text-white shadow-lg">
    <div class="container mx-auto px-4 py-3 flex justify-between">
        <a href="/" class="font-bold text-lg">Blockchain Pemilu</a>
        <div class="flex space-x-4">
            @guest
                <a href="/login" class="hover:underline">Login</a>
                <a href="/register" class="hover:underline">Register</a>
            @else
                <a href="/dashboard" class="hover:underline">Dashboard</a>
                <form method="POST" action="/logout" class="inline">
                    @csrf
                    <button type="submit" class="hover:underline">Logout</button>
                </form>
            @endguest
        </div>
    </div>
</nav>
