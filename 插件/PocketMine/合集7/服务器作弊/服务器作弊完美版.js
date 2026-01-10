
Block.setLightLevel(14,15)
Block.setShape(14,0.001,0.001,0.001,0.999,0.999,0.999)
Block.setLightLevel(15,15)
Block.setShape(15,0.001,0.001,0.001,0.999,0.999,0.999)
Block.setLightLevel(73,15)
Block.setShape(73,0.001,0.001,0.001,0.999,0.999,0.999)
Block.setLightLevel(16,15)
Block.setShape(16,0.001,0.001,0.001,0.999,0.999,0.999)
Block.setLightLevel(56,15)
Block.setShape(56,0.001,0.001,0.001,0.999,0.999,0.999)
Block.setLightLevel(21,15)
Block.setShape(21,0.001,0.001,0.001,0.999,0.999,0.999)
Block.setLightLevel(129,15)
Block.setShape(129,0.001,0.001,0.001,0.999,0.999,0.999)
var xmode=0
var qmode=0
var smode=0
var ctx=com.mojang.minecraftpe.MainActivity.currentMainActivity.get() 
ctx.runOnUiThread(new java.lang.Runnable({ 
run: function(){ 
try{
var layout=new android.widget.LinearLayout(ctx); 
var button=new android.widget.Button(ctx); 
button.setText("Q"); 
button.setOnClickListener(new android.view.View.OnClickListener() { 
onClick: function(v){ 
openMenu() 
}}); 
layout.addView(button); 
button=new android.widget.PopupWindow(layout, dip2px(ctx,85), dip2px(ctx,45)); 
button.setContentView(layout); 
button.setWidth(160); 
button.setHeight(160); 
button.showAtLocation(ctx.getWindow().getDecorView(),android.view.Gravity.RIGHT | android.view.Gravity.BOTTOM,0,0);
}catch(err){print(err)}
}}))



function dip2px(ctx, dips){ 
return Math.ceil(dips * ctx.getResources().getDisplayMetrics().density); 
}

function openMenu(){
var ctx=com.mojang.minecraftpe.MainActivity.currentMainActivity.get()
var layout=new android.widget.LinearLayout(ctx)
try{
var menu=new android.widget.PopupWindow(layout, dip2px(ctx,75), dip2px(ctx,30));
menu.setFocusable(true)
var layout=new android.widget.LinearLayout(ctx)
layout.setOrientation(1)
var va=new android.widget.TextView(ctx)
va.setText("实用工具箱")
va.setTextSize(26)
layout.addView(va)

var ve=new android.widget.TextView(ctx)
ve.setText("")
ve.setTextSize(12)
layout.addView(ve)

var vb=new android.widget.Button(ctx)
vb.setText("夜视（慎用）")
vb.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(v){
if(qmode==0){
qmode=1
print("夜视开启")
Block.setLightLevel(0,15)
Block.setLightLevel(8,15)
}
else if(qmode==1){
qmode=0
print("夜视关闭")
Block.setLightLevel(0,0)
Block.setLightLevel(8,0)
}
}}))
layout.addView(vb)


var vb=new android.widget.Button(ctx)
vb.setText("潜行")
vb.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(v){
if(xmode==0){
xmode=1
print("潜行")
Entity.setSneaking(Player.getEntity(),true)
}
else if(xmode==1){
xmode=0
print("关闭潜行")
Entity.setSneaking(Player.getEntity(),false)
}
}}))
layout.addView(vb)

var vb=new android.widget.Button(ctx)
vb.setText("飞行")
vb.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(v){
if(!Level.getGameMode()){isfly = isChecked;
			if(isfly){Player.setCanFly(true); print("扣血不关我的事")}
			if(!isfly){Player.setCanFly(false);}
			}

}}))
layout.addView(vb)

var vb=new android.widget.Button(ctx)
vb.setText("自杀")
vb.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(v){
Player.setHealth(0);
}}))
layout.addView(vb)

var vb=new android.widget.Button(ctx)
vb.setText("满血")
vb.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(v){
Player.setHealth(20);
}}))
layout.addView(vb)

var vb=new android.widget.Button(ctx)
vb.setText("炒鸡血量")
vb.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(v){
Player.setHealth(225);
}}))
layout.addView(vb)

var vb=new android.widget.Button(ctx)
vb.setText("模式")
vb.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(v){
if(Level.getGameMode()==0)
{
Level.setGameMode(1)
print("创造")
}
else if(Level.getGameMode()==1)
{
Level.setGameMode(0)
print("生存")
}
}}))
layout.addView(vb)

var vb=new android.widget.Button(ctx)
vb.setText("远视")
vb.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(v){
ModPE.setFov(10)

}}))
layout.addView(vb)

var vb=new android.widget.Button(ctx)
vb.setText("广角")
vb.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(v){
ModPE.setFov(500)

}}))
layout.addView(vb)

var vb=new android.widget.Button(ctx)
vb.setText("正常视野")
vb.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(v){
ModPE.setFov(70)

}}))
layout.addView(vb)

var vb=new android.widget.Button(ctx)
vb.setText("透视")
vb.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(v){
var x=Math.floor(Player.getX())
var y=Math.floor(Player.getY())
var z=Math.floor(Player.getZ())
var g=getTile(x,y,z)
if(g==46){
print("已关闭透视")
setTile(x,y,z,0)
}
if(g==0){
print("已开启透视")
setTile(x,y,z,46)
}
}}))
layout.addView(vb)

var vb=new android.widget.Button(ctx)
vb.setText("说明")
vb.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(v){

var ctx=com.mojang.minecraftpe.MainActivity.currentMainActivity.get() 
ctx.runOnUiThread(new java.lang.Runnable({ 
run: function(){ 
try{

var dialog=new android.app.AlertDialog.Builder(ctx)
dialog.setTitle("说明")
dialog.setMessage("功能介绍:\n1.夜视，把空气和水调亮，会比较卡，冰雪会融化，可能会出现bug。\n2.自杀，死亡但不掉落物品。\n3.飞行，可能有bug。\n4.炒鸡血量，225血量:")
dialog.setPositiveButton("返回游戏",new android.content.DialogInterface.OnClickListener(){
onClick: function(dia,w){

/*点确定时执行*/
}})
dialog.show()

/*在此添加android组件*/
}catch(err){print(err)}
}}))

}}))
layout.addView(vb)

var ve=new android.widget.TextView(ctx)
ve.setText("v1.0")
ve.setTextSize(12)
layout.addView(ve)


var mlayout=makeMenu(ctx,layout)
menu.setContentView(mlayout)
menu.setWidth(dip2px(ctx,150))
menu.setHeight(dip2px(ctx,400))
menu.setBackgroundDrawable(new android.graphics.drawable.ColorDrawable(android.graphics.Color.argb(127,0,0,0)))
menu.showAtLocation(ctx.getWindow().getDecorView(),android.view.Gravity.RIGHT | android.view.Gravity.TOP,dip2px(ctx,0),dip2px(ctx,0))
}catch(err){
print(err)
}
}


function makeMenu(ctx,layout){ 
var mlayout=new android.widget.RelativeLayout(ctx) 
var svParams=new android.widget.RelativeLayout.LayoutParams(android.widget.RelativeLayout.LayoutParams.FILL_PARENT,android.widget.RelativeLayout.LayoutParams.FILL_PARENT) 
var scrollview=new android.widget.ScrollView(ctx) 
var pad=dip2px(ctx,2) 
scrollview.setPadding(pad,pad,pad,pad) 
scrollview.setLayoutParams(svParams) 
scrollview.addView(layout) 
mlayout.addView(scrollview) 
return mlayout 
}





















