
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Drop Zone</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #0f172a; /* Slate 900 */
            font-family: 'Inter', sans-serif;
        }
        .glass-panel {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="h-screen w-full flex items-center justify-center p-4">

    <!-- Background Decoration -->
    <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl translate-y-1/2"></div>
    </div>

    <div class="glass-panel w-full max-w-md p-6 md:p-8 rounded-2xl shadow-2xl z-10">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Welcome Back</h1>
            <p class="text-slate-400">Sign in to continue sharing files</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-medium text-slate-300 mb-1">Email Address</label>
                <div class="relative">
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-slate-500 transition-all outline-none"
                        placeholder="you@example.com">
                </div>
                @error('email')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-slate-300 mb-1">Password</label>
                <div class="relative">
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-slate-500 transition-all outline-none"
                        placeholder="••••••••">
                </div>
                @error('password')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center text-slate-300 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-600 bg-slate-700 text-blue-600 focus:ring-blue-500/50 focus:ring-offset-0">
                    <span class="ml-2">Remember me</span>
                </label>
                
                @if (Route::has('password.request'))
                <!-- {{ route('password.request') }} -->
                    <a href="#" class="text-blue-400 hover:text-blue-300 transition-colors">
                        Forgot password?
                    </a>
                @endif
            </div>

            <button type="submit" 
                class="w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-semibold rounded-lg shadow-lg shadow-blue-600/30 transform hover:-translate-y-0.5 transition-all duration-200">
                Sign In
            </button>
        </form>
        
        <div class="mt-8 text-center text-sm text-slate-500">
            <p>Don't have an account? <span class="text-slate-400">Contact Admin</span></p>
        </div>
    </div>

<a href="{{ url('/') }}"
   class="fixed bottom-4 right-4 md:bottom-6 md:right-6 z-50 px-4 py-2 md:px-6 md:py-3 rounded-full 
          bg-gradient-to-r from-blue-600 to-indigo-600 
          text-white text-xs md:text-sm font-semibold shadow-xl 
          shadow-blue-600/30 hover:shadow-blue-500/50 
          transition-all duration-300 hover:-translate-y-1 
          backdrop-blur-lg flex items-center gap-2">
    👤 Go to User Panel
</a>


</body>
</html>
