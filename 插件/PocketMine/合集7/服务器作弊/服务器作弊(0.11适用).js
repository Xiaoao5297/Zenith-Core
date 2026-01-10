var ff=null
var sd=null
var dd=null
var sbb=0
var cah=0
var textview=null
var islight2=false
var islight10=false
var islight11=false
var islight3=false
var islight4=false
var islight7=false
var islight5=false
var sp=null
var sjp=5
var fjj=0
var jk=1
var jb=null
var zhishang=30
var jl=69.9
var islight9=false


//获取程序包名
var $=com.mojang.minecraftpe.MainActivity.currentMainActivity.get().getPackageName();
//如果不是Beta版或Pro版启动器，则退出
if($!="net.zhuoweizhang.mcpelauncher"&&$!="net.zhuoweizhang.mcpelauncher.pro")
{
java.lang.System.exit(0)
}


 var ahhh=new java.lang.Thread(
new java.lang.Runnable({
run: function(){
while(1){
if(fjj==1){
for(var o=0;o<100;o++){if(getTile(Player.getX(),Player.getZ()-o,Player.getZ())==jk){ clientMessage("已探测完成")
}} 
}
ahhh.sleep(50)
}}}))

ahhh.start()

var ahhha=new java.lang.Thread(
new java.lang.Runnable({
run: function(){
while(1){
if(cah==1){
Server.sendChat(sp)
}
ahhha.sleep(sjp)
}}}))

ahhha.start()

var hh=new java.lang.Thread(
new java.lang.Runnable({
run: function(){
while(1){
if(sbb==1){
ModPE.showTipMessage(Math.floor(Player.getX())+","+Math.floor(Player.getY())+","+Math.floor(Player.getZ())+","+zhishang)
}
hh.sleep(40)
}}}))
hh.start()
var cnm = com.mojang.minecraftpe.MainActivity.currentMainActivity.get();


cnm.runOnUiThread(new java.lang.Runnable(
{
run: function() 
{
try
{
ff = new android.widget.PopupWindow();
var layout = new android.widget.RelativeLayout(cnm);
var button = new android.widget.Button(cnm);
button .setText("作弊");
button.setTextSize(15);
button.setTextColor(android.graphics.Color.argb(150,0,255,255));
button.setBackgroundColor(android.graphics.Color.argb(0,255,255,255));
button.setOnClickListener(new android.view.View.OnClickListener(
{
onClick: function(viewarg) 
{
openMenu()


}
}
)
);
layout .addView(button);
                        
ff.setContentView(layout);
ff.setWidth(150);
ff.setHeight(100);
ff.showAtLocation(cnm.getWindow().getDecorView(), android.view.Gravity.LEFT| android.view.Gravity.BOTTOM,0,600);
}
catch(err)
{
print("Error: "+err);
}
} 
}
)
);  
function makeMenu(ctx,menu,layout){
  var mlayout=new android.widget.RelativeLayout(ctx)
 var svParams=new android.widget.RelativeLayout.LayoutParams(android.widget.RelativeLayout.LayoutParams.FILL_PARENT,android.widget.RelativeLayout.LayoutParams.FILL_PARENT)
var scrollview=new android.widget.ScrollView(ctx)
 var pad = dip2px(ctx,5)
 scrollview.setPadding(pad,pad,pad,pad)
 scrollview.setLayoutParams(svParams)
 scrollview.addView(layout)
 mlayout.addView(scrollview)
 return mlayout
}
function dip2px(ctx, dips){
return Math.ceil(dips * ctx.getResources().getDisplayMetrics().density);
}
function openMenu(){

 if(Level.getGameMode()==1){islight10=true}
if(Level.getGameMode()==0){islight10=false}
 var ctx=com.mojang.minecraftpe.MainActivity.currentMainActivity.get()
  try{
   var menu=new android.widget.PopupWindow()
   menu.setFocusable(true)
   mainMenu=menu
   var layout=new android.widget.LinearLayout(ctx)
   layout.setOrientation(1)

   var textParams=new android.widget.LinearLayout.LayoutParams(android.widget.RelativeLayout.LayoutParams.WRAP_CONTENT, android.widget.RelativeLayout.LayoutParams.WRAP_CONTENT)
   textParams.setMargins(dip2px(ctx, 5), 0, 0, 0)

   var title=new android.widget.TextView(ctx);
   title.setTextSize(22)
   title.setText("仙人制作服务器作弊")
   title.setTextColor(android.graphics.Color.GREEN)
   title.setLayoutParams(textParams)
   layout.addView(title)
   
   

var k1k = new android.widget.CheckBox(ctx);
k1k.setText("远视");
k1k.setTextSize(15);
k1k.setChecked(islight5);
k1k.setOnCheckedChangeListener(new android.widget.CompoundButton.OnCheckedChangeListener(){
onCheckedChanged: function(v,isChecked){
islight5 = isChecked;
if(islight5==true){

islight5=true;
var ctx = com.mojang.minecraftpe.MainActivity.currentMainActivity.get();
ctx.runOnUiThread(new java.lang.Runnable(){  
run: function(){
try{

jb = new android.widget.PopupWindow();

var layout = new android.widget.LinearLayout(ctx);
kk = new android.widget.SeekBar(ctx);
kk.setMax(79);//拖动条分为多少份
layout.setOrientation(android.widget.LinearLayout.VERTICAL);
layout.addView(kk);

jb.setContentView(layout);
jb.setHeight(android.widget.LinearLayout.LayoutParams.WRAP_CONTENT);
jb.setWidth(400);//sb长度
jb.showAtLocation(ctx.getWindow().getDecorView(), android.view.Gravity.BOTTOM, 0, 80);

/*拖动条独有的onSeekBarChange事件*/
kk.setOnSeekBarChangeListener(new android.widget.SeekBar.OnSeekBarChangeListener({
onProgressChanged:function(kk,i,b){
//拖动中数值改变时执行,i为进度
ModPE.setFov(80-i);
},
onStartTrackingTouch:function(kk){
//开始拖动时执行
},
onStopTrackingTouch:function(kk){
//结束拖动时执行
}


}))
}catch(e){
clientMessage(e)}
}})
}
if(islight5==false){

islight5=false;
var ctx = com.mojang.minecraftpe.MainActivity.currentMainActivity.get();
ctx.runOnUiThread(new java.lang.Runnable( { run:function() {
if(jb != null){
jb.dismiss();
jb = null;
}
}
}
)
);
}}});
layout.addView(k1k);



 var k1k = new android.widget.CheckBox(ctx);
k1k.setText("调整游戏速度速度");
k1k.setTextSize(15);
k1k.setChecked(islight9);
k1k.setOnCheckedChangeListener(new android.widget.CompoundButton.OnCheckedChangeListener(){
onCheckedChanged: function(v,isChecked){
islight9 = isChecked;
if(islight9==true){
var cnm = com.mojang.minecraftpe.MainActivity.currentMainActivity.get();


cnm.runOnUiThread(new java.lang.Runnable(
{
run: function() 
{
try
{
nmbv1 = new android.widget.PopupWindow();
var layout = new android.widget.RelativeLayout(cnm);
var button = new android.widget.Button(cnm);
button .setText("+");
button.setTextSize(15);
button.setTextColor(android.graphics.Color.argb(150,0,255,255));
button.setBackgroundColor(android.graphics.Color.argb(0,255,255,255));
button.setOnClickListener(new android.view.View.OnClickListener(
{
onClick: function(viewarg) 
{
zhishang++
zhishang++
zhishang++
zhishang++ 
zhishang++

ModPE.setGameSpeed(zhishang)

}
}
)
);
layout .addView(button);
                        
nmbv1.setContentView(layout);
nmbv1.setWidth(150);
nmbv1.setHeight(50);
nmbv1.showAtLocation(cnm.getWindow().getDecorView(), android.view.Gravity.RIGHT| android.view.Gravity.BOTTOM,0,50);
}
catch(err)
{
print("Error: "+err);
}
} 
}
)
);  


var cnm = com.mojang.minecraftpe.MainActivity.currentMainActivity.get();


cnm.runOnUiThread(new java.lang.Runnable(
{
run: function() 
{
try
{
nmbv2 = new android.widget.PopupWindow();
var layout = new android.widget.RelativeLayout(cnm);
var button = new android.widget.Button(cnm);
button .setText("-");
button.setTextSize(15);
button.setTextColor(android.graphics.Color.argb(150,0,255,255));
button.setBackgroundColor(android.graphics.Color.argb(0,255,255,255));
button.setOnClickListener(new android.view.View.OnClickListener(
{
onClick: function(viewarg) 
{
if(zhishang>=5){
zhishang=zhishang-5
ModPE.setGameSpeed(zhishang)}
else{clientMessage("游戏速度不能小于0")}

}
}
)
);
layout .addView(button);
                        
nmbv2.setContentView(layout);
nmbv2.setWidth(150);
nmbv2.setHeight(50);
nmbv2.showAtLocation(cnm.getWindow().getDecorView(), android.view.Gravity.RIGHT| android.view.Gravity.BOTTOM,0,0);
}
catch(err)
{
print("Error: "+err);
}
} 
}
)
);  
ModPE.setGameSpeed(zhishang)

islight9=true;

}
if(islight9==false){
 ModPE.setGameSpeed(20)
zhishang=20

var ctx=com.mojang.minecraftpe.MainActivity.currentMainActivity.get() 
ctx.runOnUiThread(new java.lang.Runnable({ 
run: function(){ 
try{
/*在此添加android组件*/
if(nmbv1!=null){nmbv1.dismiss()
nmbv2.dismiss()}
}catch(err){print(err)}
}})) 
islight9=false;

}}});
layout.addView(k1k);

var k1k = new android.widget.CheckBox(ctx);
k1k.setText("我的信息");
k1k.setTextSize(15);
k1k.setChecked(islight2);
k1k.setOnCheckedChangeListener(new android.widget.CompoundButton.OnCheckedChangeListener(){
onCheckedChanged: function(v,isChecked){
islight2 = isChecked;
if(islight2==true){

islight2=true;


 sbb=1



}
if(islight2==false){

islight2=false;
sbb=0
}}});
layout.addView(k1k);



var k1k = new android.widget.CheckBox(ctx);
k1k.setText("模式快捷键");
k1k.setTextSize(15);
k1k.setChecked(islight7);
k1k.setOnCheckedChangeListener(new android.widget.CompoundButton.OnCheckedChangeListener(){
onCheckedChanged: function(v,isChecked){
islight7 = isChecked;
if(islight7==true){

islight7=true;

var cnm = com.mojang.minecraftpe.MainActivity.currentMainActivity.get();


cnm.runOnUiThread(new java.lang.Runnable(
{
run: function() 
{
try
{
nmbv = new android.widget.PopupWindow();
var layout = new android.widget.RelativeLayout(cnm);
var button = new android.widget.Button(cnm);
button .setText("模式");
button.setTextSize(15);
button.setTextColor(android.graphics.Color.argb(150,0,255,255));
button.setBackgroundColor(android.graphics.Color.argb(0,255,255,255));
button.setOnClickListener(new android.view.View.OnClickListener(
{
onClick: function(viewarg) 
{
if(Level.getGameMode()==1){ Level.setGameMode(0)
}
else if(Level.getGameMode()==0){ Level.setGameMode(1)
} 

}
}
)
);
layout .addView(button);
                        
nmbv.setContentView(layout);
nmbv.setWidth(150);
nmbv.setHeight(100);
nmbv.showAtLocation(cnm.getWindow().getDecorView(), android.view.Gravity.LEFT| android.view.Gravity.BOTTOM,0,250);
}
catch(err)
{
print("Error: "+err);
}
} 
}
)
);  
 



}
if(islight7==false){

islight7=false;

var ctx=com.mojang.minecraftpe.MainActivity.currentMainActivity.get() 
ctx.runOnUiThread(new java.lang.Runnable({ 
run: function(){ 
try{
/*在此添加android组件*/
if(nmbv!=null){nmbv.dismiss()}
}catch(err){print(err)}
}})) 

}}});
layout.addView(k1k); 


 var k1k = new android.widget.CheckBox(ctx);
k1k.setText("生存/创造");
k1k.setTextSize(15);
k1k.setChecked(islight10);
k1k.setOnCheckedChangeListener(new android.widget.CompoundButton.OnCheckedChangeListener(){
onCheckedChanged: function(v,isChecked){
islight10 = isChecked;
if(islight10==true){

islight10=true;
Level.setGameMode(1)

}
if(islight10==false){

islight10=false;
Level.setGameMode(0)
}}});
layout.addView(k1k);

 var k1k = new android.widget.CheckBox(ctx);
k1k.setText("透视");
k1k.setTextSize(15);
k1k.setChecked(islight3);
k1k.setOnCheckedChangeListener(new android.widget.CompoundButton.OnCheckedChangeListener(){
onCheckedChanged: function(v,isChecked){
islight3 = isChecked;
if(islight3==true){

islight3=true;


print("开")
for(var lll=1;lll<255;lll++){
Block.setRenderLayer(lll,1);}




}
if(islight3==false){

islight3=false;
for(var lll=1;lll<255;lll++){
Block.setRenderLayer(lll,0);}
}}});
layout.addView(k1k);



 var k1k = new android.widget.CheckBox(ctx);
k1k.setText("刷屏");
k1k.setTextSize(15);
k1k.setChecked(islight4);
k1k.setOnCheckedChangeListener(new android.widget.CompoundButton.OnCheckedChangeListener(){
onCheckedChanged: function(v,isChecked){
islight4 = isChecked;
if(islight4==true){

islight4=true;


 cah=1



}
if(islight4==false){

islight4=false;
cah=0
}}});
layout.addView(k1k);


var k1k = new android.widget.CheckBox(ctx);
k1k.setText("探测");
k1k.setTextSize(15);
k1k.setChecked(islight11);
k1k.setOnCheckedChangeListener(new android.widget.CompoundButton.OnCheckedChangeListener(){
onCheckedChanged: function(v,isChecked){
islight11 = isChecked;
if(islight11==true){

islight11=true;


 fjj=1



}
if(islight11==false){

islight11=false;
fjj=0
}}});
layout.addView(k1k);

var input=new android.widget.EditText(ctx);
input.setHint("设置游戏速度");
layout.addView(input);
var button=new android.widget.Button(ctx);
button.setText("目前为:"+zhishang);
button.setTextColor(android.graphics.Color.argb(150,0,255,255));
button.setBackgroundColor(android.graphics.Color.argb(0,255,255,255));
button.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(){
var hear=input.getText().toString();//获取文本框内容
zhishang=hear
if(islight9==true){ModPE.setGameSpeed(zhishang)}
button.setText("目前为:"+zhishang*10);
menu.dismiss()

}}))
layout.addView(button);

var fv=new android.widget.EditText(ctx);
fv.setHint("设刷屏内容");
layout.addView(fv);
var button=new android.widget.Button(ctx);
button.setText("目前为:"+sp);
button.setTextColor(android.graphics.Color.argb(150,0,255,255));

button.setBackgroundColor(android.graphics.Color.argb(0,255,255,255));
button.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(){
sp=fv.getText().toString();//获取文本框内容
menu.dismiss()
button.setText("目前为:"+sp);
}}))
layout.addView(button);

var fvc=new android.widget.EditText(ctx);
fvc.setHint("设置探测id");
layout.addView(fvc);
var button=new android.widget.Button(ctx);
button.setText("目前为:"+jk);
button.setTextColor(android.graphics.Color.argb(150,0,255,255));
button.setBackgroundColor(android.graphics.Color.argb(0,255,255,255));
button.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(){
jk=fvc.getText().toString();//获取文本框内容
button.setText("目前为:"+jk);
menu.dismiss()
}}))
layout.addView(button);

var fk=new android.widget.EditText(ctx);
fk.setHint("设置刷频速度");
layout.addView(fk);
var button=new android.widget.Button(ctx);
button.setText("目前为:"+sjp);
button.setTextColor(android.graphics.Color.argb(150,0,255,255));
button.setBackgroundColor(android.graphics.Color.argb(0,255,255,255));
button.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(){
sjp=fk.getText().toString();//获取文本框内容
menu.dismiss()
button.setText("目前为:"+sjp);
}}))
layout.addView(button);



var fvck=new android.widget.EditText(ctx);
fvck.setHint("设置视野");
layout.addView(fvck);
var button=new android.widget.Button(ctx);
button.setText("目前为:"+jl);
button.setTextColor(android.graphics.Color.argb(150,0,255,255));
button.setBackgroundColor(android.graphics.Color.argb(0,255,255,255));
button.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(){
jl=fvck.getText().toString();//获取文本框内容
button.setText("目前为:"+jl);
menu.dismiss()
try{
ModPE.setFov(jl)}
catch(err){
clientMessage("输入错误")}
}}))
layout.addView(button); 
 

 


 

var button3=new android.widget.Button(ctx)
button3.setText("退出游戏")
button3.setTextColor(android.graphics.Color.argb(255,255,255,0));
button3.setBackgroundColor(android.graphics.Color.argb(30,255,255,255));
button3.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(v){
var ctx=com.mojang.minecraftpe.MainActivity.currentMainActivity.get()
ctx.runOnUiThread(new java.lang.Runnable({run: function(){ctx.finish()}}))
}}))
layout.addView(button3)




var button3=new android.widget.Button(ctx)
button3.setText("恢复默认视角")
button3.setTextColor(android.graphics.Color.argb(255,255,255,0));
button3.setBackgroundColor(android.graphics.Color.argb(30,255,255,255));
button3.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(v){
jl=69.9
ModPE.setFov(69.9) 
}}))
layout.addView(button3)


var button3=new android.widget.Button(ctx)
button3.setText("使用说明")
button3.setTextColor(android.graphics.Color.argb(255,255,255,0));
button3.setBackgroundColor(android.graphics.Color.argb(30,255,255,255));
button3.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(v){
hhh()
}}))
layout.addView(button3)

var button3=new android.widget.Button(ctx)
button3.setText("访问作者贴吧")
button3.setTextColor(android.graphics.Color.argb(255,255,255,0));
button3.setBackgroundColor(android.graphics.Color.argb(30,255,255,255));
button3.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(v){
ctx.startActivity(new android.content.Intent(android.content.Intent.ACTION_VIEW,android.net.Uri.parse("http://tieba.baidu.com/CNL工作室"))) 
}}))
layout.addView(button3) 




    var mlayout=makeMenu(ctx,menu,layout)
   menu.setContentView(mlayout)
   menu.setWidth(ctx.getWindowManager().getDefaultDisplay().getWidth()/4)
   menu.setHeight(ctx.getWindowManager().getDefaultDisplay().getHeight())
   menu.setBackgroundDrawable(new android.graphics.drawable.ColorDrawable(android.graphics.Color.TRANSPARENT))
   menu.showAtLocation(ctx.getWindow().getDecorView(),android.view.Gravity.RIGHT | android.view.Gravity.TOP,0,0)
 }catch(err){
    print(err)
 }

}

function hhh(){

var ctx=com.mojang.minecraftpe.MainActivity.currentMainActivity.get() 
ctx.runOnUiThread(new java.lang.Runnable({ 
run: function(){ 
try{
/*在此添加android组件*/
var dialog=new android.app.AlertDialog.Builder(ctx)
dialog.setTitle("使用说明")
dialog.setMessage("本服务器作弊仙人制作，请不要破坏服务器和谐。参数设置一定要合理，不要设置过大或过小。要飞行请改创造，不要飞行时间太长。刷频参数越小刷的越快，但部分服务器禁止说话太快。探测方块是探测脚下的100格。若出现已探测完成请垂直往下挖，便可找到想要的方块。不要在本地使用此js，在我的信息中，前三个是自己坐标(x轴坐标在前，y轴次之，z轴在最后)，第四个是当前游戏速度。当启动修改模式快捷键时，出现模式按钮，单击模式修改模式。单击修改游戏速度后，游戏速度变为30,右下角出现+,-单击＋速度＋5，单击－速度－5,正常速度为20，也可在输入框内设置速度。")
dialog.setPositiveButton("确定",new android.content.DialogInterface.OnClickListener(){
onClick: function(dia,w){
/*点确定时执行*/
}})
dialog.show()

}catch(err){print(err)}
}}))} 

var ctx=com.mojang.minecraftpe.MainActivity.currentMainActivity.get() 
ctx.runOnUiThread(new java.lang.Runnable({ 
run: function(){ 
try{
/*在此添加android组件*/
var dialog=new android.app.AlertDialog.Builder(ctx)
dialog.setTitle("使用说明")
dialog.setMessage("本服务器作弊仙人制作，请不要破坏服务器和谐。参数设置一定要合理，不要设置过大或过小。要飞行请改创造，不要飞行时间太长。刷频参数越小刷的越快，但部分服务器禁止说话太快。探测方块是探测脚下的100格。若出现已探测完成请垂直往下挖，便可找到想要的方块。不要在本地使用此js，在我的信息中，前三个是自己坐标(x轴坐标在前，y轴次之，z轴在最后)，第四个是当前游戏速度。当启动修改模式快捷键时，出现模式按钮，单击模式修改模式。单击修改游戏速度后，游戏速度变为30,右下角出现+,-单击＋速度＋5，单击－速度－5,正常速度为20，也可在输入框内设置速度。")
dialog.setPositiveButton("确定",new android.content.DialogInterface.OnClickListener(){
onClick: function(dia,w){
/*点确定时执行*/
}})
dialog.show()

}catch(err){print(err)}
}}))



