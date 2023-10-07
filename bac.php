clientId = "77700054";
apiKey = "vjtKWhj2sHc3kNW2A3WI1wZVh0nqMsPInSxAhX3Gqp37ACA7XZyUW2";


PowerTranz Gateway Api 1.3 ---> https://staging.ptranz.com/api/swagger/index.html

Testear llamadas api ---> https://www.postman.com/dark-space-610460/workspace/fac-payment-gateway/request/14225525-cad29a2c-aaea-4c50-917a-73650b4b5c33


ACTIVIDAD DEL CARRITO:

package com.vaqueras.delivery.activity;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.database.Cursor;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.DatePicker;
import android.widget.TimePicker;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;
import android.media.MediaPlayer;
import java.util.Calendar;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AlertDialog;
import androidx.core.widget.NestedScrollView;
import androidx.recyclerview.widget.DefaultItemAnimator;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.bumptech.glide.Glide;
import com.vaqueras.delivery.R;
import com.vaqueras.delivery.adepter.CartAdp;
import com.vaqueras.delivery.map.OrderTrackerActivity;
import com.vaqueras.delivery.model.Address;
import com.vaqueras.delivery.model.Cart;
import com.vaqueras.delivery.model.MenuitemDataItem;
import com.vaqueras.delivery.model.MyAddress;
import com.vaqueras.delivery.model.PaymentItem;
import com.vaqueras.delivery.model.RestResponse;
import com.vaqueras.delivery.model.Tips;
import com.vaqueras.delivery.model.User;
import com.vaqueras.delivery.retrofit.APIClient;
import com.vaqueras.delivery.utiles.GetResult;
import com.vaqueras.delivery.utiles.CustPrograssbar;
import com.vaqueras.delivery.utiles.MyHelper;
import com.vaqueras.delivery.utiles.SessionManager;
import com.google.android.material.snackbar.Snackbar;
import com.google.android.material.bottomsheet.BottomSheetDialog;
import com.google.gson.Gson;
import com.google.gson.JsonObject;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.text.DecimalFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.List;
import java.util.Locale;

import butterknife.BindView;
import butterknife.ButterKnife;
import butterknife.OnClick;
import okhttp3.MediaType;
import okhttp3.RequestBody;
import retrofit2.Call;

import static com.vaqueras.delivery.utiles.SessionManager.coupon;
import static com.vaqueras.delivery.utiles.SessionManager.couponid;
import static com.vaqueras.delivery.utiles.SessionManager.currency;
import static com.vaqueras.delivery.utiles.SessionManager.istax;
import static com.vaqueras.delivery.utiles.SessionManager.istip;
import static com.vaqueras.delivery.utiles.SessionManager.login;
import static com.vaqueras.delivery.utiles.SessionManager.taxs;
import static com.vaqueras.delivery.utiles.SessionManager.tips;
import static com.vaqueras.delivery.utiles.SessionManager.wallet;
import static com.vaqueras.delivery.utiles.SessionManager.walletname;
import static com.vaqueras.delivery.utiles.Utility.newAddress;
import static com.vaqueras.delivery.utiles.Utility.paymentId;
import static com.vaqueras.delivery.utiles.Utility.paymentsucsses;
import static com.vaqueras.delivery.utiles.Utility.tragectionID;

public class CartActivity extends BaseActivity implements CartAdp.RecyclerTouchListener, GetResult.MyListener {

    MediaPlayer mediaPlayer;

    @BindView(R.id.txt_process)
    TextView txt_process;
    @BindView(R.id.appbar)
    com.google.android.material.appbar.AppBarLayout appbar;
    @BindView(R.id.toolbar)
    android.widget.Toolbar toolbar;
    @BindView(R.id.recycler_cart)
    RecyclerView recyclerCart;
    @BindView(R.id.recycler_tips)
    RecyclerView recyclerTips;
    @BindView(R.id.ed_note)
    EditText edNote;
    @BindView(R.id.et_date)
    EditText etDate;
    @BindView(R.id.et_hour)
    EditText etHour;
    @BindView(R.id.et_details)
    EditText etDetails;
    @BindView(R.id.txt_total)
    TextView txtTotal;
    @BindView(R.id.txt_type)
    TextView txtType;
    @BindView(R.id.txt_address)
    TextView txtAddress;
    @BindView(R.id.txt_location)
    TextView txtLocation;
    @BindView(R.id.txt_title)
    TextView txtTitle;
    @BindView(R.id.img_rest)
    ImageView imgRest;
    @BindView(R.id.img_coopncode)
    ImageView imgCoopncode;
    @BindView(R.id.txt_itemtotal)
    TextView txtItemtotal;
    @BindView(R.id.txt_dcharge)
    TextView txtDcharge;
    @BindView(R.id.lvl_discount)
    LinearLayout lvlDiscount;
    @BindView(R.id.txt_discount)
    TextView txtDiscount;
    @BindView(R.id.txt_applycode)
    TextView txtApplycode;
    @BindView(R.id.txt_scharge)
    TextView txtScharge;
    @BindView(R.id.txt_topay)
    TextView txtTopay;
    @BindView(R.id.txt_deliverytip)
    TextView txtDeliverytip;
    @BindView(R.id.txt_tax)
    TextView txtTax;
    @BindView(R.id.lvl_main)
    LinearLayout lvlMain;
    @BindView(R.id.lvl_bottom)
    LinearLayout lvlBottom;
    @BindView(R.id.lvl_notfound)
    LinearLayout lvlNotfound;
    @BindView(R.id.lvl_dtips)
    LinearLayout lvlDtips;
    @BindView(R.id.lvl_dtipview)
    LinearLayout lvlDtipview;
    @BindView(R.id.lvl_taxs)
    LinearLayout lvlTaxs;
    @BindView(R.id.lvl_notlogin)
    LinearLayout lvlNotlogin;
    @BindView(R.id.lvl_resprent)
    LinearLayout lvlResprent;
    @BindView(R.id.lvl_storeclose)
    LinearLayout lvlStoreclose;
    @BindView(R.id.lvl_addaadress)
    LinearLayout lvlAddaadress;
    @BindView(R.id.lvl_wallet)
    LinearLayout lvlWallet;
    @BindView(R.id.txt_walletname)
    TextView txtWalletname;
    @BindView(R.id.ch_wallet)
    CheckBox chWallet;
    @BindView(R.id.btn_login)
    TextView btnLogin;
    @BindView(R.id.lvl_twal)
    LinearLayout lvlTwal;
    @BindView(R.id.lvl_scharge)
    LinearLayout lvlScharge;
    @BindView(R.id.txt_waltea)
    TextView txtWaltea;
    @BindView(R.id.srcollview)
    NestedScrollView srcollview;

    private String orderID;

    private int orderCount = 1;
    MyHelper myHelper;
    public CartAdp adapter;
    MyAddress myAddress;
    User user;
    SessionManager sessionManager;
    public static CartActivity activity;
    CustPrograssbar custPrograssbar;
    List<PaymentItem> paymentList = new ArrayList<>();

    public static CartActivity getInstance() {
        return activity;
    }

    List<MenuitemDataItem> itemList;
    String rid;
    Cart cart;
    double total = 0;
    double itemtotal = 0;
    List<Tips> tipsList;
    Spinner spinnerMonth;
    Spinner spinnerDay;
    Spinner spinnerHour;

    boolean isMonthSelected = false;
    boolean isDaySelected = false;
    boolean isHourSelected = false;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_cart);
        ButterKnife.bind(this);
        activity = this;
        custPrograssbar = new CustPrograssbar();
        sessionManager = new SessionManager(this);
        sessionManager.setIntData(coupon, 0);
        mediaPlayer = MediaPlayer.create(this, R.raw.noti);
        myAddress = sessionManager.getAddress();
        user = sessionManager.getUserDetails("");

        LinearLayout layoutDelivery = findViewById(R.id.click_delivery);
        layoutDelivery.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                edNote.requestFocus();
            }
        });

        LinearLayout layoutPickup = findViewById(R.id.click_pickup);
        layoutPickup.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                edNote.requestFocus();
            }
        });

        // Inicio de Spinners
        spinnerMonth = findViewById(R.id.spinnerMonth);
        spinnerDay = findViewById(R.id.spinnerDay);
        spinnerHour = findViewById(R.id.spinnerHour);

        configureSpinners();

        // Mostrar el diálogo
        showAlertDialog("Para continuar con la compra, por favor, programa en el formulario la fecha de retiro, de lo contrario el botón no procederá.");

        txt_process = findViewById(R.id.txt_process);

    }

    private void configureSpinners() {
        // Obtener el mes actual
        Calendar calendar = Calendar.getInstance();
        int currentMonth = calendar.get(Calendar.MONTH);

        // Configurar el adaptador y el título para el Spinner de los meses
        ArrayAdapter<String> monthAdapter = new ArrayAdapter<>(this, android.R.layout.simple_spinner_dropdown_item,
                new String[]{"Mes", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto",
                        "Septiembre", "Octubre", "Noviembre", "Diciembre"});
        spinnerMonth.setAdapter(monthAdapter);

        // Establecer el mes en el Spinner
        spinnerMonth.setSelection(0);

        // Configurar el adaptador y el título para el Spinner de los días
        ArrayAdapter<String> dayAdapter = new ArrayAdapter<>(this, android.R.layout.simple_spinner_dropdown_item,
                getDayList());
        spinnerDay.setAdapter(dayAdapter);

        // Configurar el adaptador y el título para el Spinner de las horas con minutos
        List<String> hourList = new ArrayList<>();
        hourList.add("Hora");

        for (int hour = 12; hour <= 23; hour++) {
            if (hour == 23) {
                // Si es 11 P.M., solo agregar la opción 11:00 P.M. y salir del bucle
                String am_pm = "P.M.";
                hourList.add("11:00 " + am_pm);
                break;
            }

            for (int minute = 0; minute < 60; minute += 15) {
                String am_pm = (hour < 12) ? "A.M." : "P.M.";
                int displayHour = (hour == 12) ? 12 : hour % 12;
                String time = String.format("%d:%02d %s", displayHour, minute, am_pm);
                hourList.add(time);
            }
        }

        ArrayAdapter<String> hourAdapter = new ArrayAdapter<>(this, android.R.layout.simple_spinner_dropdown_item, hourList);
        spinnerHour.setAdapter(hourAdapter);

        updateJsonObject("", "", ""); // Actualizar el valor inicial en edNote

        spinnerMonth.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                String selectedMonth = spinnerMonth.getSelectedItem().toString();
                String selectedDay = spinnerDay.getSelectedItem().toString();
                String selectedHour = spinnerHour.getSelectedItem().toString();
                updateJsonObject(selectedMonth, selectedDay, selectedHour);
                updateDaySpinner();

                // Verificar si se han seleccionado opciones en los tres spinners
                isMonthSelected = !selectedMonth.equals("Mes");
                // Habilitar o deshabilitar el botón según el estado de los spinners
                txt_process.setEnabled(isMonthSelected && isDaySelected && isHourSelected);
            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {
                isMonthSelected = false;
                txt_process.setEnabled(false);
            }
        });

        spinnerDay.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                String selectedMonth = spinnerMonth.getSelectedItem().toString();
                String selectedDay = spinnerDay.getSelectedItem().toString();
                String selectedHour = spinnerHour.getSelectedItem().toString();
                updateJsonObject(selectedMonth, selectedDay, selectedHour);

                // Verificar si se han seleccionado opciones en los tres spinners
                isDaySelected = !selectedDay.equals("Día");
                // Habilitar o deshabilitar el botón según el estado de los spinners
                txt_process.setEnabled(isMonthSelected && isDaySelected && isHourSelected);
            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {
                isDaySelected = false;
                txt_process.setEnabled(false);
            }
        });

        spinnerHour.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                String selectedMonth = spinnerMonth.getSelectedItem().toString();
                String selectedDay = spinnerDay.getSelectedItem().toString();
                String selectedHour = spinnerHour.getSelectedItem().toString();
                updateJsonObject(selectedMonth, selectedDay, selectedHour);

                // Verificar si se han seleccionado opciones en los tres spinners
                isHourSelected = !selectedHour.equals("Hora");
                // Habilitar o deshabilitar el botón según el estado de los spinners
                txt_process.setEnabled(isMonthSelected && isDaySelected && isHourSelected);
            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {
                isHourSelected = false;
                txt_process.setEnabled(false);
            }
        });
    }

    private List<String> getDayList() {
        List<String> dayList = new ArrayList<>();
        dayList.add("Día");
        for (int i = 1; i <= 31; i++) {
            dayList.add(String.valueOf(i));
        }
        return dayList;
    }

    private void updateDaySpinner() {
        int selectedMonth = spinnerMonth.getSelectedItemPosition();
        int currentYear = Calendar.getInstance().get(Calendar.YEAR);
        int daysInMonth = getDaysInMonth(selectedMonth, currentYear);

        List<String> dayList = new ArrayList<>();
        dayList.add("Día");
        for (int i = 1; i <= daysInMonth; i++) {
            dayList.add(String.valueOf(i));
        }

        ArrayAdapter<String> dayAdapter = new ArrayAdapter<>(this, android.R.layout.simple_spinner_dropdown_item, dayList);
        spinnerDay.setAdapter(dayAdapter);
    }

    private int getDaysInMonth(int month, int year) {
        Calendar calendar = Calendar.getInstance();
        calendar.set(Calendar.MONTH, month - 1);
        calendar.set(Calendar.YEAR, year);
        return calendar.getActualMaximum(Calendar.DAY_OF_MONTH);
    }

    private void updateJsonObject(String selectedMonth, String selectedDay, String selectedHour) {
        String note = "";
        if (!selectedMonth.equals("Mes")) {
            note += selectedMonth;
        }
        if (!selectedDay.equals("Día")) {
            if (!note.isEmpty()) {
                note += ", ";
            }
            note += selectedDay;
        }
        if (!selectedHour.equals("Hora")) {
            if (!note.isEmpty()) {
                note += ", ";
            }
            note += selectedHour;
        }

        edNote.setText(note);
        // Fin de Spinners

        myHelper = new MyHelper(CartActivity.this);
        LinearLayoutManager mLayoutManager = new LinearLayoutManager(this);
        mLayoutManager.setOrientation(LinearLayoutManager.VERTICAL);
        recyclerCart.setLayoutManager(mLayoutManager);

        LinearLayoutManager mLayoutManager1 = new LinearLayoutManager(this);
        mLayoutManager1.setOrientation(LinearLayoutManager.HORIZONTAL);
        recyclerTips.setLayoutManager(mLayoutManager1);
        itemList = new ArrayList<>();
        itemList = myHelper.getCData();
        adapter = new CartAdp(this, itemList, this);
        recyclerCart.setItemAnimator(new DefaultItemAnimator());
        recyclerCart.setAdapter(adapter);
        if (itemList.size() != 0) {
            lvlBottom.setVisibility(View.VISIBLE);
            lvlMain.setVisibility(View.VISIBLE);
            lvlNotfound.setVisibility(View.GONE);
            rid = itemList.get(0).getRid();

            if (sessionManager.getIntData(istip) == 1) {
                String[] separated = sessionManager.getStringData(tips).split(",");
                tipsList = new ArrayList<>();
                for (String price : separated) {
                    Tips tips = new Tips();
                    tips.setPrice(Integer.parseInt(price));
                    tips.setSelect(false);
                    tipsList.add(tips);
                }

                recyclerTips.setAdapter(new TipsAdp(this));


            } else {
                lvlDtips.setVisibility(View.GONE);
            }
            if (myAddress.getType() != null) {
                txtType.setText("" + myAddress.getType());
                txtAddress.setText("" + myAddress.getHno() + "," + myAddress.getLandmark() + "," + myAddress.getAddress());

            } else {
                if (sessionManager.getBooleanData(login)) {
                    getAddress();
                } else {
                    lvlBottom.setVisibility(View.GONE);
                    lvlNotlogin.setVisibility(View.VISIBLE);
                }
            }
            getHome(rid);
        } else {
            lvlBottom.setVisibility(View.GONE);
            lvlMain.setVisibility(View.GONE);
            lvlNotfound.setVisibility(View.VISIBLE);
        }

        // Poner: (lvlWallet.setVisibility(View.VISIBLE);), si se desea mostrar el saldo de la billetera.

        if (sessionManager.getIntData(wallet) != 0) {
            txtWalletname.setText(sessionManager.getStringData(walletname));
            chWallet.setText(sessionManager.getStringData(currency) + sessionManager.getIntData(wallet));
            lvlWallet.setVisibility(View.GONE);
        }

        chWallet.setOnCheckedChangeListener((buttonView, isChecked) -> CartActivity.this.updateCartData());
    }

    private void getHome(String rid) {
        custPrograssbar.prograssCreate(this);
        JSONObject jsonObject = new JSONObject();
        try {
            jsonObject.put("rid", rid);
            jsonObject.put("lats", myAddress.getLatMap());
            jsonObject.put("longs", myAddress.getLongMap());
        } catch (Exception e) {
            e.printStackTrace();
        }
        RequestBody bodyRequest = RequestBody.create(MediaType.parse("application/json"), jsonObject.toString());
        Call<JsonObject> call = APIClient.getInterface().getcartData(bodyRequest);
        GetResult getResult = new GetResult();
        getResult.setMyListener(this);
        getResult.callForLogin(call, "1");

    }

    private void getAddress() {

        custPrograssbar.prograssCreate(this);
        JSONObject jsonObject = new JSONObject();
        try {
            jsonObject.put("uid", user.getId());

        } catch (Exception e) {
            e.printStackTrace();
        }
        RequestBody bodyRequest = RequestBody.create(MediaType.parse("application/json"), jsonObject.toString());
        Call<JsonObject> call = APIClient.getInterface().addressList(bodyRequest);
        GetResult getResult = new GetResult();
        getResult.setMyListener(this);
        getResult.callForLogin(call, "3");
    }

    public void updateCount() {
        adapter = new CartAdp(this, myHelper.getCData(), this);
        recyclerCart.setAdapter(adapter);
        updateCartData();
    }

    BottomSheetDialog mBottomSheetDialog;

    public void selectLocation(Context context, List<MyAddress> list) {
        Activity activity = (Activity) context;
        mBottomSheetDialog = new BottomSheetDialog(activity);
        View sheetView = activity.getLayoutInflater().inflate(R.layout.cust_address_select, null);

        RecyclerView rvList = sheetView.findViewById(R.id.rv_list);
        TextView txtAddaddress = sheetView.findViewById(R.id.txt_addaddress);
        LinearLayoutManager layoutManager = new LinearLayoutManager(this, LinearLayoutManager.VERTICAL, false);
        rvList.setLayoutManager(layoutManager);
        rvList.setAdapter(new AdepterAddress(this, list));
        txtAddaddress.setOnClickListener(v -> {
            newAddress = 1;
            mBottomSheetDialog.cancel();
            startActivity(new Intent(CartActivity.this, MapActivity.class));
        });
        mBottomSheetDialog.setContentView(sheetView);
        mBottomSheetDialog.setCancelable(false);
        mBottomSheetDialog.show();


    }

    double tempWallet = 0;
    DecimalFormat df = new DecimalFormat("#.##");

    public void updateCartData() {
        total = 0;
        itemtotal = 0;
        double aprice = 0;
        List<MenuitemDataItem> itemList = myHelper.getCData();
        if (itemList.size() != 0) {
            lvlBottom.setVisibility(View.VISIBLE);
            lvlMain.setVisibility(View.VISIBLE);
            lvlNotfound.setVisibility(View.GONE);

            for (int i = 0; i < itemList.size(); i++) {
                MenuitemDataItem item = myHelper.getCData().get(i);
                aprice = 0;
                if (item.getAddonPrice() != null) {
                    String[] separated = item.getAddonPrice().split(",");
                    for (String price : separated) {
                        aprice = aprice + Integer.parseInt(price);
                    }
                }
                double temptotal = aprice + item.getPrice();
                itemtotal = itemtotal + (temptotal * item.getQty());
            }
            txtItemtotal.setText(sessionManager.getStringData(currency) + df.format(itemtotal));
            total = itemtotal + Double.parseDouble(cart.getRestuarantData().get(0).getRestDcharge());
            if (sessionManager.getIntData(coupon) != 0) {
                imgCoopncode.setImageResource(R.drawable.ic_coupon_close);
            } else {
                imgCoopncode.setImageResource(R.drawable.ic_coupon_arrow);
            }
            txtDcharge.setText(sessionManager.getStringData(currency) + df.format(Double.parseDouble(cart.getRestuarantData().get(0).getRestDcharge())));
            if (cart.getRestuarantData().get(0).getRestCharge().equalsIgnoreCase("0")) {
                lvlScharge.setVisibility(View.GONE);
            } else {
                txtScharge.setText(sessionManager.getStringData(currency) + df.format(cart.getRestuarantData().get(0).getRestCharge()));
            }

            if (sessionManager.getIntData(coupon) == 0) {
                lvlDiscount.setVisibility(View.GONE);
                txtApplycode.setText(R.string.apply_coupon);
                txtApplycode.setTextColor(getResources().getColor(R.color.black));

            } else {
                txtApplycode.setText(R.string.coupom_apply);
                txtApplycode.setTextColor(getResources().getColor(R.color.colorgreen));
                lvlDiscount.setVisibility(View.VISIBLE);
                txtDiscount.setText(sessionManager.getStringData(currency) + sessionManager.getIntData(coupon));
            }

            total = total + Double.parseDouble(cart.getRestuarantData().get(0).getRestCharge());
            total = total - sessionManager.getIntData(coupon);
            if (tipsList.get(pposition).isSelect()) {
                lvlDtips.setVisibility(View.VISIBLE);
                total = total + tipsList.get(pposition).getPrice();
                txtDeliverytip.setText(sessionManager.getStringData(currency) + tipsList.get(pposition).getPrice());
            } else {
                lvlDtips.setVisibility(View.GONE);

            }
            if (sessionManager.getIntData(istax) == 1) {
                lvlTaxs.setVisibility(View.VISIBLE);
                txtTax.setText(sessionManager.getStringData(currency) + sessionManager.getStringData(taxs));
                total = total + Integer.parseInt(sessionManager.getStringData(taxs));

            } else {
                lvlTaxs.setVisibility(View.GONE);
            }
            if (user.getIsVerify() == 1) {
                if (chWallet.isChecked()) {

                    if (sessionManager.getIntData(SessionManager.wallet) <= total) {

                        total = total - sessionManager.getIntData(SessionManager.wallet);
                        chWallet.setText(sessionManager.getStringData(SessionManager.currency) + "0");
                        tempWallet = sessionManager.getIntData(SessionManager.wallet);
                    } else {
                        tempWallet = sessionManager.getIntData(SessionManager.wallet) - total;
                        chWallet.setText(sessionManager.getStringData(SessionManager.currency) + tempWallet);
                        tempWallet = total;
                        total = 0;

                    }
                    lvlTwal.setVisibility(View.VISIBLE);
                    txtWaltea.setText(sessionManager.getStringData(SessionManager.currency) + tempWallet);
                } else {
                    lvlTwal.setVisibility(View.GONE);
                    tempWallet = 0;
                    chWallet.setText(sessionManager.getStringData(currency) + sessionManager.getIntData(wallet));
                }
            }


            String totalr = df.format(total);
            txtTopay.setText(sessionManager.getStringData(currency) + totalr);
            txtTotal.setText(sessionManager.getStringData(currency) + totalr);

        } else {
            lvlBottom.setVisibility(View.GONE);
            lvlMain.setVisibility(View.GONE);
            lvlNotfound.setVisibility(View.VISIBLE);
        }


    }


    @Override
    public void onCartItem(String titel, int position) {
    }

    @OnClick()
    public void onClickback(View view) {
        finish();
    }

    @OnClick({R.id.txt_viewll, R.id.btn_addaddres, R.id.txt_change, R.id.txt_process, R.id.img_coopncode, R.id.btn_login, R.id.lvl_storeclose})
    public void onClick(View view) {
        Log.d("CartActivity", "onClick called for view with ID: " + view.getId());

        switch (view.getId()) {
            case R.id.btn_addaddres:
                newAddress = 1;
                startActivity(new Intent(this, MapActivity.class));
                break;
            case R.id.txt_change:
                getAddress();
                break;
            case R.id.txt_process:
                // Verificar si los spinners están llenos antes de realizar la acción
                if (!isMonthSelected || !isDaySelected || !isHourSelected) {
                    // Si los spinners no están seleccionados, mostrar el mensaje para seleccionar una fecha de retiro
                    showAlertDialog("Por favor, programa una fecha de retiro");
                } else if (Integer.parseInt(cart.getRestuarantData().get(0).getRestMorder()) <= itemtotal) {
                    if (total == 0) {
                        paymentId = "5";
                        new AsyncTaskRunner().execute("0");
                    } else {
                        bottonPaymentList();
                    }
                } else {
                    // Si no se cumple la condición de compra mínima, mostrar el mensaje con la cantidad mínima de pedido
                    showAlertDialog("Cantidad mínima de pedido " + cart.getRestuarantData().get(0).getRestMorder());
                }
                break;
            case R.id.lvl_storeclose:
                finish();
                break;
            case R.id.txt_viewll:
                srcollview.fullScroll(View.FOCUS_DOWN);
                break;
            case R.id.btn_login:
                startActivity(new Intent(CartActivity.this, LoginActivity.class));
                break;
            case R.id.img_coopncode:
                if (sessionManager.getIntData(coupon) != 0) {
                    imgCoopncode.setImageResource(R.drawable.ic_cancel_coupon);
                    sessionManager.setIntData(coupon, 0);
                    updateCartData();
                } else {
                    int temp = (int) Math.round(total);
                    startActivity(new Intent(CartActivity.this, CoupunActivity.class).putExtra("rid", rid).putExtra("amount", temp));
                }
                break;
            default:
                throw new IllegalStateException("Unexpected value: " + view.getId());
        }
    }

    private void showAlertDialog(String message) {
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setTitle("Alerta del Carrito")
                .setMessage(message)
                .setPositiveButton("Cerrar", new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialog, int which) {
                        // Acción a realizar cuando el usuario presione "OK"
                    }
                });
        AlertDialog dialog = builder.create();
        dialog.show();
    }

    public void bottonPaymentList() {
        BottomSheetDialog mBottomSheetDialog = new BottomSheetDialog(this);
        View sheetView = getLayoutInflater().inflate(R.layout.custome_payment, null);
        LinearLayout listView = sheetView.findViewById(R.id.lvl_list);
        TextView txtTotal = sheetView.findViewById(R.id.txt_total);
        txtTotal.setText(getString(R.string.itmetoal) + " " + sessionManager.getStringData(currency) + df.format(total));
        for (int i = 0; i < paymentList.size(); i++) {
            LayoutInflater inflater = LayoutInflater.from(CartActivity.this);
            PaymentItem paymentItem = paymentList.get(i);
            View view = inflater.inflate(R.layout.custome_paymentitem, null);
            ImageView imageView = view.findViewById(R.id.img_icon);
            TextView txtTitle = view.findViewById(R.id.txt_title);
            TextView txtSubtitel = view.findViewById(R.id.txt_subtitel);
            txtTitle.setText("" + paymentList.get(i).getmTitle());
            txtSubtitel.setText("" + paymentList.get(i).getSubtitle());
            Glide.with(CartActivity.this).load(APIClient.baseUrl + "/" + paymentList.get(i).getmImg()).thumbnail(Glide.with(CartActivity.this).load(R.drawable.animationbg)).into(imageView);
            int finalI = i;
            view.setOnClickListener(v -> {
                paymentId = paymentList.get(finalI).getmId();
                try {
                    switch (paymentList.get(finalI).getmTitle()) {
                        case "EFECTIVO":
                            new AsyncTaskRunner().execute("0");
                            mBottomSheetDialog.cancel();
                            mediaPlayer.start();
                            break;

                        case "YAPPY":
                            new AsyncTaskRunner().execute("1");
                            mBottomSheetDialog.cancel();
                            mediaPlayer.start();
                            break;

                        case "PAGUELO FACIL":
                            mBottomSheetDialog.cancel();

                            // Obtén el monto total del carrito desde tu lógica
                            double totalAmount = calcularMontoTotalCarrito();

                            // Crea un Intent para iniciar PagueloFacilActivity
                            Intent PagueloFacilIntent = new Intent(CartActivity.this, PagueloFacilActivity.class);

                            // Pasa la cantidad total como extra en el Intent
                            PagueloFacilIntent.putExtra("TOTAL_AMOUNT", totalAmount);

                            // Inicia PagueloFacilActivity
                            startActivity(PagueloFacilIntent);
                            break;

                        default:
                            break;
                    }

                } catch (Exception e) {
                    e.printStackTrace();
                }
            });
            listView.addView(view);
        }
        mBottomSheetDialog.setContentView(sheetView);
        mBottomSheetDialog.show();
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (requestCode == 1 && resultCode == RESULT_OK) {
            boolean executeEfectivo = data.getBooleanExtra("executeEfectivo", false);
            if (executeEfectivo) {
                // Ejecutar el botón "EFECTIVO"
            }
        }
    }

    private double calcularMontoTotalCarrito() {
        double totalAmount = 0.0;
        if (itemList != null) {
            for (MenuitemDataItem item : itemList) {
                totalAmount += item.getPrice() * item.getQuantity();
            }
        }
        return totalAmount;
    }


    @Override
    protected void onDestroy() {
        super.onDestroy();
        if (mediaPlayer != null) {
            mediaPlayer.release();
            mediaPlayer = null;
        }
    }

    private class AsyncTaskRunner extends AsyncTask<String, String, JSONArray> {


        @Override
        protected JSONArray doInBackground(String... params) {
            JSONArray jsonArray = new JSONArray();
            Cursor res = myHelper.getAllData();
            while (res.moveToNext()) {
                JSONObject jsonObject = new JSONObject();
                try {
                    double aprice = 0;
                    String temp = res.getString(13);
                    if (temp != null && !temp.isEmpty()) {
                        String[] separated = temp.split(",");
                        for (String price : separated) {
                            aprice = aprice + Double.parseDouble(price);
                        }
                    }
                    aprice = aprice + Double.parseDouble(res.getString(6));
                    jsonObject.put("pid", res.getString(2));
                    jsonObject.put("title", res.getString(3));
                    jsonObject.put("cost", df.format(aprice));
                    jsonObject.put("qty", res.getString(7));
                    jsonObject.put("addon", res.getString(12));
                    jsonObject.put("type", res.getString(10));
                    jsonArray.put(jsonObject);

                } catch (JSONException e) {
                    e.printStackTrace();
                }
            }
            return jsonArray;
        }


        @Override
        protected void onPostExecute(JSONArray jsonArray) {
            // execution of result of Long time consuming operation
            orderPlace(jsonArray);
        }

        @Override
        protected void onPreExecute() {

        }

        @Override
        protected void onProgressUpdate(String... text) {

        }
    }

    public void orderPlace(JSONArray jsonArray) {
        custPrograssbar.prograssCreate(CartActivity.this);
        JSONObject jsonObject = new JSONObject();
        try {
            EditText edNote = findViewById(R.id.ed_note);
            EditText etDate = findViewById(R.id.et_date);
            EditText etHour = findViewById(R.id.et_hour);
            EditText etDetails = findViewById(R.id.et_details);

            jsonObject.put("rest_id", rid);
            jsonObject.put("uid", user.getId());
            jsonObject.put("p_method_id", paymentId);
            jsonObject.put("full_address", myAddress.getHno() + myAddress.getLandmark() + myAddress.getAddress());
            jsonObject.put("atype", myAddress.getType());
            jsonObject.put("d_charge", cart.getRestuarantData().get(0).getRestDcharge());
            jsonObject.put("cou_id", sessionManager.getIntData(couponid));
            jsonObject.put("cou_amt", sessionManager.getIntData(coupon));
            jsonObject.put("transaction_id", tragectionID);
            jsonObject.put("product_total", df.format(total));
            jsonObject.put("product_subtotal", df.format(itemtotal));
            jsonObject.put("a_note", edNote.getText().toString());
            jsonObject.put("a_date", etDate.getText().toString());
            jsonObject.put("a_hour", etHour.getText().toString());
            jsonObject.put("a_details", etDetails.getText().toString());
            jsonObject.put("wall_amt", tempWallet);
            jsonObject.put("tax", sessionManager.getStringData(taxs));
            if (tipsList.get(pposition).isSelect()) {
                jsonObject.put("tip", tipsList.get(pposition).getPrice());
            } else {
                jsonObject.put("tip", "0");
            }
            jsonObject.put("rest_charge", cart.getRestuarantData().get(0).getRestCharge());
            jsonObject.put("lats", myAddress.getLatMap());
            jsonObject.put("longs", myAddress.getLongMap());
            jsonObject.put("ProductData", jsonArray);

            RequestBody bodyRequest = RequestBody.create(MediaType.parse("application/json"), jsonObject.toString());
            Call<JsonObject> call = APIClient.getInterface().getOrderNow(bodyRequest);
            GetResult getResult = new GetResult();
            getResult.setMyListener(this);
            getResult.callForLogin(call, "2");
        } catch (JSONException e) {
            e.printStackTrace();
        }
    }

    @Override
    public void callback(JsonObject result, String callNo) {
        try {
            custPrograssbar.closePrograssBar();
            if (callNo.equalsIgnoreCase("1")) {
                Gson gson = new Gson();
                cart = gson.fromJson(result.toString(), Cart.class);
                if (cart.getResult().equalsIgnoreCase("true")) {
                    Glide.with(this).load(APIClient.baseUrl + "/" + cart.getRestuarantData().get(0).getRestImg()).thumbnail(Glide.with(this).load(R.drawable.animationbg)).into(imgRest);
                    txtTitle.setText("" + cart.getRestuarantData().get(0).getRestTitle());
                    txtLocation.setText("" + cart.getRestuarantData().get(0).getRestFullAddress());
                    paymentList = new ArrayList<>();
                    paymentList = cart.getPaymentItems();
                    if (cart.getRestuarantData().get(0).getRestIsopen() == 0) {
                        lvlStoreclose.setVisibility(View.VISIBLE);
                        lvlBottom.setVisibility(View.GONE);
                    }
                    updateCartData();
                }
            } else if (callNo.equalsIgnoreCase("2")) {
                Gson gson = new Gson();
                RestResponse restResponse = gson.fromJson(result.toString(), RestResponse.class);
                sessionManager.setIntData(wallet, Integer.parseInt(restResponse.getWallet()));
                if (restResponse.getResult().equalsIgnoreCase("true")) {
                    myHelper.deleteCard();
                    sessionManager.setIntData(wallet, Integer.parseInt(restResponse.getWallet()));
                    Intent intent = new Intent(this, OrderTrackerActivity.class).putExtra("oid", restResponse.getOrderId());
                    startActivity(intent);
                    finish();

                } else {
                    finish();
                    Toast.makeText(this, restResponse.getResponseMsg(), Toast.LENGTH_LONG).show();
                }
            } else if (callNo.equalsIgnoreCase("3")) {

                Gson gson = new Gson();
                Address address = gson.fromJson(result.toString(), Address.class);
                if (address.getResult().equalsIgnoreCase("true")) {
                    selectLocation(this, address.getAddressList());
                } else {
                    lvlAddaadress.setVisibility(View.VISIBLE);
                    lvlBottom.setVisibility(View.GONE);
                }
            }

        } catch (Exception e) {
            e.toString();

        }
    }

    @Override
    protected void onResume() {
        super.onResume();
        if (cart != null) {
            updateCartData();
        }
        if (newAddress == 1) {
            newAddress = 0;
            if (myAddress.getType() != null) {
                txtType.setText("" + myAddress.getType());
                txtAddress.setText("" + myAddress.getHno() + "," + myAddress.getLandmark() + "," + myAddress.getAddress());

            } else {
                if (sessionManager.getBooleanData(login)) {
                    getAddress();
                } else {
                    lvlBottom.setVisibility(View.GONE);
                    lvlNotlogin.setVisibility(View.VISIBLE);
                }
            }
        }


        if (paymentsucsses == 1) {
            paymentsucsses = 0;
            new AsyncTaskRunner().execute("0");

        }
    }

    int pposition = 0;

    public class TipsAdp extends RecyclerView.Adapter<TipsAdp.MyViewHolder> {
        private Context mContext;


        public class MyViewHolder extends RecyclerView.ViewHolder {
            public TextView title;

            public MyViewHolder(View view) {
                super(view);
                title = (TextView) view.findViewById(R.id.txt_title);
            }
        }

        public TipsAdp(Context mContext) {
            this.mContext = mContext;


        }

        @Override
        public TipsAdp.MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
            View itemView = LayoutInflater.from(parent.getContext())
                    .inflate(R.layout.item_tips, parent, false);
            return new TipsAdp.MyViewHolder(itemView);
        }

        @Override
        public void onBindViewHolder(final TipsAdp.MyViewHolder holder, @SuppressLint("RecyclerView") int position) {

            Tips item = tipsList.get(position);
            holder.title.setText("" + item.getPrice());
            if (item.isSelect) {
                holder.title.setBackground(mContext.getDrawable(R.drawable.tip1));
                holder.title.setTextColor(mContext.getResources().getColor(R.color.colorPrimary));
                holder.title.setCompoundDrawablesWithIntrinsicBounds(null, null, mContext.getDrawable(R.drawable.ic_tip_close), null);
            } else {
                holder.title.setBackground(mContext.getDrawable(R.drawable.tip0));
                holder.title.setTextColor(mContext.getResources().getColor(R.color.black));
                holder.title.setCompoundDrawablesWithIntrinsicBounds(null, null, null, null);

            }
            holder.title.setOnClickListener(v -> {
                if (item.isSelect()) {

                    pposition = position;
                    item.setSelect(false);
                    tipsList.set(position, item);
                    notifyDataSetChanged();
                } else {
                    Tips tips = tipsList.get(pposition);
                    tips.setSelect(false);
                    tipsList.set(pposition, tips);
                    pposition = position;
                    item.setSelect(true);
                    tipsList.set(position, item);
                    notifyDataSetChanged();
                }
                updateCartData();

            });
        }

        @Override
        public int getItemCount() {
            return tipsList.size();

        }
    }

    public class AdepterAddress extends RecyclerView.Adapter<AdepterAddress.BannerHolder> {
        private Context context;
        private List<MyAddress> listItems;

        public AdepterAddress(Context context, List<MyAddress> mBanner) {
            this.context = context;
            this.listItems = mBanner;
        }

        @NonNull
        @Override
        public AdepterAddress.BannerHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
            View view = LayoutInflater.from(context).inflate(R.layout.addresss_item1, parent, false);
            return new AdepterAddress.BannerHolder(view);
        }

        @Override
        public void onBindViewHolder(@NonNull AdepterAddress.BannerHolder holder, int position) {
            holder.txtType.setText("" + listItems.get(position).getType());
            holder.txtFulladdress.setText("" + listItems.get(position).getHno() + "," + listItems.get(position).getLandmark() + "," + listItems.get(position).getAddress());
            Glide.with(context).load(APIClient.baseUrl + "/" + listItems.get(position).getAddressImage()).into(holder.imgBanner);
            holder.lvlHome.setOnClickListener(v -> {
                MyAddress myAddress = listItems.get(position);
                sessionManager.setAddress(myAddress);
                if (mBottomSheetDialog != null)
                    mBottomSheetDialog.cancel();
                recreate();
            });

        }

        @Override
        public int getItemCount() {
            return listItems.size();
        }

        public class BannerHolder extends RecyclerView.ViewHolder {
            @BindView(R.id.img_banner)
            ImageView imgBanner;

            @BindView(R.id.txt_type)
            TextView txtType;
            @BindView(R.id.txt_fulladdress)
            TextView txtFulladdress;

            @BindView(R.id.lvl_home)
            LinearLayout lvlHome;

            public BannerHolder(@NonNull View itemView) {
                super(itemView);
                ButterKnife.bind(this, itemView);
            }
        }
    }
}



          ACTIVIDAD DE PAGUELO FACIL, BASICAMENTE EL BOTÓN DEL CARRITO LLEVA A LA ACTIVIDAD DE PAGUELO FACIL QUE ES ESTA:

          package com.vaqueras.delivery.activity;

import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import androidx.appcompat.app.AppCompatActivity;

import com.vaqueras.delivery.R;
import com.vaqueras.delivery.utiles.Utility;

public class PagueloFacilActivity extends AppCompatActivity {

    private WebView webView;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_paguelofacil);

        // Obtén el monto total del carrito de la actividad anterior
        double totalAmount = getIntent().getDoubleExtra("TOTAL_AMOUNT", 0.0);

        // Configura tu URL de PagueloFacil (cambia la URL de acuerdo a la documentación)
        String pagueloFacilUrl = "https://sandbox.paguelofacil.com/LinkDeamon.cfm";

        // Inicializa el WebView
        webView = findViewById(R.id.webView);
        WebSettings webSettings = webView.getSettings();
        webSettings.setJavaScriptEnabled(true);

        // Carga la URL de PagueloFacil
        webView.loadUrl(pagueloFacilUrl);

        // Configura un WebViewClient para manejar las redirecciones internas
        webView.setWebViewClient(new WebViewClient() {
            @Override
            public boolean shouldOverrideUrlLoading(WebView view, String url) {
                if (url != null && url.startsWith("https://checkout.paguelofacil.com")) {
                    // Detecta la URL de redirección de PagueloFacil
                    handlePaymentResponse(url);
                    return true;
                }
                return super.shouldOverrideUrlLoading(view, url);
            }
        });
    }

    // Método para manejar la respuesta de pago de PagueloFacil
    private void handlePaymentResponse(String redirectUrl) {
        // Aquí puedes realizar acciones basadas en la respuesta de PagueloFacil
        // Por ejemplo, verificar si el pago fue exitoso o rechazado
        // y mostrar un mensaje apropiado al usuario
        if (redirectUrl.contains("code=LK-RQC5CO5G3TLNNJUU")) {
            // El pago fue exitoso
            Utility.tragectionID = "payment_success_id";
            Utility.paymentsucsses = 1;
            finish(); // Cierra esta actividad
        } else {
            // El pago fue rechazado
            showPaymentRejectedDialog();
        }
    }

    // Método para mostrar un diálogo de pago exitoso
    private void showPaymentSuccessDialog() {
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setTitle("Pago Exitoso");
        builder.setMessage("¡El pago se ha procesado con éxito!");

        // Agregar un botón de "Aceptar"
        builder.setPositiveButton("Aceptar", new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
                // Acciones a realizar al hacer clic en "Aceptar"
            }
        });

        // Mostrar el diálogo
        AlertDialog dialog = builder.create();
        dialog.show();
    }

    // Método para mostrar un diálogo de pago rechazado
    private void showPaymentRejectedDialog() {
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setTitle("Pago Rechazado");
        builder.setMessage("Lo sentimos, el pago ha sido rechazado.");

        // Agregar un botón de "Aceptar"
        builder.setPositiveButton("Aceptar", new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
                // Acciones a realizar al hacer clic en "Aceptar"
            }
        });

        // Mostrar el diálogo
        AlertDialog dialog = builder.create();
        dialog.show();
    }
}
