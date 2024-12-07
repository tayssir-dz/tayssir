<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Auto Sales</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans">
    <!-- Navigation -->
    <nav class="bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="#" class="text-white font-bold text-xl">Premium Auto</a>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="#home" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Home</a>
                        <a href="#vehicles" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Vehicles</a>
                        <a href="#about" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">About</a>
                        <a href="#contact" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Contact</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative bg-gray-900 h-screen" id="home">
        <div class="absolute inset-0">
            <img class="w-full h-full object-cover" src="https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?ixlib=rb-4.0.3" alt="Luxury Car">
            <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
        </div>
        <div class="relative max-w-7xl mx-auto py-24 px-4 sm:py-32 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl text-center">Find Your Dream Car</h1>
            <p class="mt-6 text-xl text-gray-300 text-center max-w-3xl mx-auto">Discover our exclusive collection of premium vehicles</p>
            <div class="mt-10 flex justify-center">
                <a href="#vehicles" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Browse Inventory
                </a>
            </div>
        </div>
    </div>

    <!-- Featured Vehicles -->
    <section class="py-16 bg-white" id="vehicles">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-extrabold text-gray-900 text-center mb-12">Featured Vehicles</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Vehicle Card 1 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden transform transition duration-500 hover:-translate-y-1">
                    <img class="w-full h-48 object-cover" src="https://images.unsplash.com/photo-1555215695-3004980ad54e?ixlib=rb-4.0.3" alt="Luxury Sedan">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900">Luxury Sedan</h3>
                        <p class="mt-2 text-gray-600">Experience ultimate comfort and style with our premium sedan collection.</p>
                        <a href="#" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            Learn More
                        </a>
                    </div>
                </div>

                <!-- Vehicle Card 2 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden transform transition duration-500 hover:-translate-y-1">
                    <img class="w-full h-48 object-cover" src="https://images.unsplash.com/photo-1519245659620-e859806a8d3b?ixlib=rb-4.0.3" alt="Premium SUV">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900">Premium SUV</h3>
                        <p class="mt-2 text-gray-600">Perfect blend of luxury and versatility for your family adventures.</p>
                        <a href="#" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            Learn More
                        </a>
                    </div>
                </div>

                <!-- Vehicle Card 3 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden transform transition duration-500 hover:-translate-y-1">
                    <img class="w-full h-48 object-cover" src="https://images.unsplash.com/photo-1553440569-bcc63803a83d?ixlib=rb-4.0.3" alt="Sports Car">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900">Sports Car</h3>
                        <p class="mt-2 text-gray-600">Feel the thrill of performance with our sports car collection.</p>
                        <a href="#" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            Learn More
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-16 bg-gray-50" id="contact">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-extrabold text-gray-900 text-center mb-12">Contact Us</h2>
            <div class="max-w-lg mx-auto">
                <div class="bg-white shadow-lg rounded-lg p-8">
                    <form class="space-y-6">
                        <div>
                            <input type="text" placeholder="Your Name" class="block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <input type="email" placeholder="Your Email" class="block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <textarea rows="4" placeholder="Your Message" class="block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-base text-gray-400">&copy; 2024 Premium Auto. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
