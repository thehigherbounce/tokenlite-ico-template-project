<div class="modal fade" id="addTnx">
    <div class="modal-dialog modal-dialog-md modal-dialog-centered">
        <div class="modal-content">
            <a href="#" class="modal-close" data-dismiss="modal" aria-label="Close"><em class="ti ti-close"></em></a>
            <div class="popup-body popup-body-md">
                <h3 class="popup-title">Manually Add Tokens</h3>
                <form action="{{ route('admin.ajax.transactions.add') }}" method="POST" class="validate-modern" id="add_token" autocomplete="off">
                    @csrf
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label">Tranx Type</label>
                                <div class="input-wrap">
                                    <select name="type" class="select select-block select-bordered" required>
                                        <option value="purchase">Purchase</option>
                                        <option value="bonus">Bonus</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-item input-with-label w-sm-60">
                                <label class="input-item-label">Tranx Date</label>
                                <div class="input-wrap">
                                    <input class="input-bordered date-picker" required="" type="text" name="tnx_date" value="{{ date('m/d/Y') }}">
                                    <span class="input-icon input-icon-right date-picker-icon"><em class="ti ti-calendar"></em></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label">Token Added To</label>
                                <div class="input-wrap">
                                    <select name="user" required="" class="select-block select-bordered"
                                        @if ($user)
                                        readonly
                                        @else
                                        data-dd-class="search-on"
                                        @endif
                                    >
                                        @if ($user)
                                        <option value="{{ $user->id }}">{{ set_id($user->id) }} - {{ $user->email }}</option>
                                        @else
                                        @forelse($users as $user)
                                        <option value="{{ $user->id }}">{{ set_id($user->id) }} - {{ $user->email }}</option>
                                        @empty
                                        <option value="">No user found</option>
                                        @endforelse
                                        @endif
                                    </select>
                                    <span class="input-note">Select account to add token.</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label">Token for Stage</label>
                                <div class="input-wrap">
                                    <select name="stage" class="select select-block select-bordered" required>
                                        @forelse($stages as $stage)
                                        <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                                        @empty
                                        <option value="">No active stage</option>
                                        @endif
                                    </select>
                                    <span class="input-note">Select Stage where from adjust tokens.</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label">Payment Gateway</label>
                                <div class="input-wrap">
                                    <select name="payment_method" class="select select-block select-bordered">
                                        @foreach($pmethods as $pmn)
                                        <option value="{{ $pmn->payment_method }}">{{ ucfirst($pmn->payment_method) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span class="input-note">Select method for this transaction.</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label">Payment Amount</label>
                                <div class="row flex-n guttar-10px">
                                    <div class="col-7">
                                        <div class="input-wrap">
                                            <input class="input-bordered" type="number" name="amount" placeholder="Optional">
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <div class="input-wrap">
                                            <select name="currency" class="select select-block select-bordered">
                                                @foreach($pm_currency as $gt => $full)
                                                @if(token('purchase_'.$gt) == 1)
                                                <option value="{{ strtoupper($gt) }}"{{ base_currency()==$gt ? ' selected=""' : '' }}>{{ strtoupper($gt) }}</option>
                                                @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <span class="input-note">Amount calculate based on stage if leave blank.</span>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="input-item input-with-label">
                                <label class="input-item-label">Payment Address</label>
                                <div class="input-wrap">
                                    <input class="input-bordered" type="text" name="wallet_address" placeholder="Optional">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label">Number of Token</label>
                                <div class="input-wrap">
                                    <input class="input-bordered" type="number" name="total_tokens" max="{{ active_stage()->max_purchase }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label d-none d-sm-inline-block">&nbsp;</label>
                                <div class="input-wrap input-wrap-checkbox mt-sm-2">
                                    <input id="auto-bonus" class="input-checkbox input-checkbox-md" type="checkbox" name="bonus_calc">
                                    <label for="auto-bonus"><span>Bonus Adjusted from Stage</span></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Token</button>
                    <div class="gaps-3x"></div>
                    <div class="note note-plane note-light">
                        <em class="fas fa-info-circle"></em>
                        <p>If checked <strong>'Bonus Adjusted'</strong>, it will applied bonus based on selected stage (only for Purchase type).</p>
                    </div>
                </form>
            </div>
        </div>{{-- .modal-content --}}
    </div>{{-- .modal-dialog --}}
</div>
{{-- Modal End --}}
