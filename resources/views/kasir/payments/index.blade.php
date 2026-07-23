@extends('layouts.app')

@section('title', 'Pembayaran - Haland PetCare')

@section('content')
<div x-data="paymentsApp()" class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pembayaran</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola dan proses pembayaran pelanggan</p>
        </div>
        <button @click="showProcessModal = true; resetPaymentForm()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700 transition-colors shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Proses Pembayaran
        </button>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form method="GET" action="{{ route('kasir.payments.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-500 mb-1">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="block w-full px-3 py-2 text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-500 mb-1">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="block w-full px-3 py-2 text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-500 mb-1">Metode Bayar</label>
                <select name="payment_method" class="block w-full px-3 py-2 text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua</option>
                    <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Tunai</option>
                    <option value="card" {{ request('payment_method') === 'card' ? 'selected' : '' }}>Kartu</option>
                    <option value="transfer" {{ request('payment_method') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                    <option value="qris" {{ request('payment_method') === 'qris' ? 'selected' : '' }}>QRIS</option>
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select name="status" class="block w-full px-3 py-2 text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua</option>
                    <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>Pending</option>
                    <option value="COMPLETED" {{ request('status') === 'COMPLETED' ? 'selected' : '' }}>Selesai</option>
                    <option value="FAILED" {{ request('status') === 'FAILED' ? 'selected' : '' }}>Gagal</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg text-sm font-medium hover:bg-gray-900 transition-colors">
                    Filter
                </button>
                <a href="{{ route('kasir.payments.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Payments Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-200">
                        <th class="px-6 py-3">No. Pembayaran</th>
                        <th class="px-6 py-3">No. Invoice</th>
                        <th class="px-6 py-3">Pelanggan</th>
                        <th class="px-6 py-3">Jumlah</th>
                        <th class="px-6 py-3">Metode</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-blue-600">
                                <a href="{{ route('kasir.payments.show', $payment) }}" class="hover:underline">{{ $payment->payment_number }}</a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $payment->payable->invoice_number ?? $payment->payable->order_number ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $payment->payable->customer->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-700 uppercase">{{ $payment->payment_method }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($payment->status === 'COMPLETED')
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">Selesai</span>
                                @elseif($payment->status === 'PENDING')
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">Pending</span>
                                @else
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">{{ ucfirst($payment->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('kasir.payments.show', $payment) }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <svg class="w-12 h-12 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                <p class="text-gray-500 mt-3 text-sm">Tidak ada data pembayaran</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $payments->withQueryString()->links() }}
            </div>
        @endif
    </div>

    {{-- Process Payment Modal --}}
    <div x-show="showProcessModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4"
         @click.self="showProcessModal = false">
        <div x-show="showProcessModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-2xl shadow-xl max-w-lg w-full">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800">Proses Pembayaran</h3>
                <button @click="showProcessModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('kasir.payments.process') }}" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Pembayaran</label>
                    <select name="payable_type" x-model="payableType" required
                            class="block w-full px-3 py-2 text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="visit">Kunjungan</option>
                        <option value="billing">Billing</option>
                        <option value="pos_order">Pesanan POS</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Invoice / Order</label>
                    <input type="text" x-model="invoiceSearch" @input.debounce.300ms="searchInvoice()"
                           class="block w-full px-3 py-2 text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Masukkan nomor invoice..." required>
                    <input type="hidden" name="payable_id" :value="selectedPayableId">
                    <template x-if="invoiceResults.length > 0">
                        <div class="mt-1 border border-gray-200 rounded-lg max-h-40 overflow-y-auto">
                            <template x-for="inv in invoiceResults" :key="inv.id">
                                <button type="button" @click="selectInvoice(inv)"
                                        class="w-full text-left px-3 py-2 text-sm hover:bg-gray-100 border-b border-gray-50 last:border-0">
                                    <span class="font-medium text-gray-800" x-text="inv.number"></span>
                                    <span class="text-gray-500 ml-2" x-text="inv.customer_name || ''"></span>
                                    <span class="text-green-600 ml-2 font-medium" x-text="'Rp ' + formatRupiah(inv.remaining || inv.total)"></span>
                                </button>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- Invoice Details --}}
                <template x-if="selectedInvoice">
                    <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Pelanggan</span>
                            <span class="font-medium text-gray-800" x-text="selectedInvoice.customer_name"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Total Tagihan</span>
                            <span class="font-medium text-gray-800" x-text="'Rp ' + formatRupiah(selectedInvoice.total)"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Sudah Dibayar</span>
                            <span class="font-medium text-gray-800" x-text="'Rp ' + formatRupiah(selectedInvoice.paid || 0)"></span>
                        </div>
                        <div class="flex justify-between text-sm border-t border-gray-200 pt-2">
                            <span class="text-gray-700 font-semibold">Sisa Bayar</span>
                            <span class="font-bold text-blue-600" x-text="'Rp ' + formatRupiah(selectedInvoice.remaining || selectedInvoice.total)"></span>
                        </div>
                    </div>
                </template>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                    <select name="payment_method" required
                            class="block w-full px-3 py-2 text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="cash">Tunai</option>
                        <option value="card">Kartu</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Bayar</label>
                    <input type="number" name="amount" x-model="paymentAmount" min="0" required
                           class="block w-full px-3 py-2 text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0">
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" @click="showProcessModal = false"
                            class="flex-1 py-2.5 px-4 rounded-xl text-sm font-medium border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            :disabled="!selectedPayableId"
                            class="flex-1 py-2.5 px-4 rounded-xl text-sm font-bold bg-blue-600 text-white hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        Proses
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function paymentsApp() {
    return {
        showProcessModal: false,
        payableType: 'visit',
        invoiceSearch: '',
        invoiceResults: [],
        selectedPayableId: null,
        selectedInvoice: null,
        paymentAmount: 0,

        formatRupiah(value) {
            return new Intl.NumberFormat('id-ID').format(Math.round(value || 0));
        },

        async searchInvoice() {
            if (this.invoiceSearch.length < 2) {
                this.invoiceResults = [];
                return;
            }
            try {
                const response = await fetch(`/api/invoices/search?q=${encodeURIComponent(this.invoiceSearch)}&type=${this.payableType}`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                if (response.ok) {
                    this.invoiceResults = await response.json();
                }
            } catch (e) {
                this.invoiceResults = [];
            }
        },

        selectInvoice(inv) {
            this.selectedPayableId = inv.id;
            this.selectedInvoice = inv;
            this.paymentAmount = inv.remaining || inv.total;
            this.invoiceSearch = inv.number;
            this.invoiceResults = [];
        },

        resetPaymentForm() {
            this.payableType = 'visit';
            this.invoiceSearch = '';
            this.invoiceResults = [];
            this.selectedPayableId = null;
            this.selectedInvoice = null;
            this.paymentAmount = 0;
        }
    };
}
</script>
@endpush
