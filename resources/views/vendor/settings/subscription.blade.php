@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Subscription & Billing</h2>

    {{-- Success/Error Messages from Controller --}}
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Validation Error!</strong>
            <span class="block sm:inline">Please check your input.</span>
            <ul class="mt-3 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex border-b border-gray-200 mb-8 space-x-6">
        {{-- Settings Tabs --}}
        <a href="{{ route('settings.show') }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="general">General</a>
        <a href="{{ route('settings.profile') }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="profile">Profile</a>
        <a href="{{ route('settings.notifications') }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="notifications">Notifications</a>
        <a href="{{ route('settings.integrations') }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="integrations">Integrations</a>
        <a href="{{ route('settings.users.index') }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="users">Users & Roles</a>
        <span class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-[#EA3A26] border-[#EA3A26]" data-tab="subscription">Subscription</span>
    </div>

    <div id="settings-tab-content-subscription" class="tab-content bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Your Current Plan</h3>
        <div class="flex flex-col md:flex-row items-center justify-between bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
            <div class="mb-4 md:mb-0">
                <p class="text-lg font-bold text-chili-red">{{ $currentPlan->name ?? 'N/A' }}</p>
                <p class="text-sm text-gray-600">{{ $currentPlan->description ?? 'No active subscription plan.' }}</p>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold text-gray-900">${{ number_format($currentPlan->price ?? 0, 2) }} <span class="text-sm text-gray-500">/ month</span></p>
                @if($currentPlan->next_billing_date ?? false)
                    <p class="text-sm text-gray-600">Next billing: {{ $currentPlan->next_billing_date->format('M d, Y') }}</p>
                @endif
            </div>
        </div>

        <h3 class="text-xl font-semibold text-gray-800 mb-4 mt-8">Change Your Plan</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($availablePlans as $plan)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 flex flex-col justify-between
                    @if($currentPlan && $currentPlan->id === $plan->id) border-chili-red ring-2 ring-chili-red @endif">
                    <div>
                        <h4 class="text-2xl font-bold text-gray-900 mb-2">{{ $plan->name }}</h4>
                        <p class="text-gray-600 mb-4">{{ $plan->description }}</p>
                        <p class="text-4xl font-extrabold text-gray-900 mb-4">${{ number_format($plan->price, 2) }}<span class="text-base font-normal text-gray-500">/month</span></p>
                        <ul class="list-disc list-inside text-gray-700 space-y-2 mb-6">
                            @foreach(explode(';', $plan->features) as $feature)
                                <li>{{ trim($feature) }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div>
                        @if($currentPlan && $currentPlan->id === $plan->id)
                            <button class="w-full bg-gray-300 text-gray-800 py-3 rounded-md font-semibold cursor-not-allowed" disabled>Current Plan</button>
                            <form action="{{ route('vendor.subscription.cancel') }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel your subscription? This action cannot be undone.');" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full text-center text-red-600 hover:underline text-sm py-2">Cancel Subscription</button>
                            </form>
                        @else
                            <form action="{{ route('vendor.subscription.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                <button type="submit" class="w-full bg-chili-red text-white py-3 rounded-md font-semibold hover:bg-tangelo transition-colors duration-200">
                                    {{ $currentPlan ? 'Upgrade/Downgrade' : 'Choose Plan' }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 py-4 col-span-3">No subscription plans available at this time.</p>
            @endforelse
        </div>

        @if($invoices->isNotEmpty())
            <h3 class="text-xl font-semibold text-gray-800 mb-4 mt-8">Billing History</h3>
            <div class="bg-white rounded-lg shadow-md overflow-x-auto mb-8">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($invoices as $invoice)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $invoice->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $invoice->issue_date->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($invoice->total_amount, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($invoice->status == 'Paid') bg-green-100 text-green-800
                                        @elseif($invoice->status == 'Unpaid') bg-yellow-100 text-yellow-800
                                        @elseif($invoice->status == 'Overdue') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $invoice->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('invoices.show', $invoice->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                    {{-- Add download or pay button if applicable --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $invoices->links() }}
            </div>
        @else
            <p class="text-center text-gray-500 py-4 mt-8">No billing history found.</p>
        @endif
    </div>

    <script>
        // Set active settings tab
        document.addEventListener('DOMContentLoaded', () => {
            const settingsTabs = document.querySelectorAll('.settings-tab');
            settingsTabs.forEach(tab => {
                tab.classList.remove('text-[#EA3A26]', 'border-[#EA3A26]');
                tab.classList.add('text-gray-500', 'border-transparent');
            });
            // Set 'subscription' as active
            document.querySelector('.settings-tab[data-tab="subscription"]').classList.add('text-[#EA3A26]', 'border-[#EA3A26]');
            document.querySelector('.settings-tab[data-tab="subscription"]').classList.remove('text-gray-500', 'border-transparent');
        });
    </script>
@endsection