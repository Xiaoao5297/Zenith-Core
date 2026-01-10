/* By Chunyulin
   贴吧:暴漫金馆     来自BOX工作室的js编写菜鸟
  QQ:1051213119 
  
^  禁止擅自修改js版权信息！  */

var ctx=com.mojang.minecraftpe.MainActivity.currentMainActivity.get()
var Version=[1,2,274]
var Color=ChatColor.YELLOW
var shf,beiod,shout=1,eaed=4,Gamespeed=1
var Aniu=[false,false,false,false,false,false,true,false,false,false,false,false,false,false,false,false,true,false,false,false]

Block.setLightLevel(14,15)
Block.setLightLevel(15,15)
Block.setLightLevel(16,15)
Block.setLightLevel(21,15)
Block.setLightLevel(56,15)
Block.setLightLevel(73,15)
Block.setLightLevel(129,15)
Block.setShape
(14,0.001,0.001,0.001,0.999,0.999,0.999)
Block.setShape
(15,0.001,0.001,0.001,0.999,0.999,0.999)
Block.setShape
(16,0.001,0.001,0.001,0.999,0.999,0.999)
Block.setShape
(21,0.001,0.001,0.001,0.999,0.999,0.999)
Block.setShape
(56,0.001,0.001,0.001,0.999,0.999,0.999)
Block.setShape
(73,0.001,0.001,0.001,0.999,0.999,0.999)
Block.setShape
(129,0.001,0.001,0.001,0.999,0.999,0.999)

function attackHook(w,e){
if(getCarriedItem()==280&&Aniu[7]==true){
preventDefault();rideAnimal(w,e)}}

function useItem(x,y,z,i,b,s,it,bl){
if(i==280&&Aniu[6]==true){
if(b!=58&&b!=61&&b!=62&&b!=54&&b!=245&&b!=64&&b!=96&&b!=71&&b!=63&&b!=92){
if(Aniu[16]==true){
Level.addParticle(ParticleType.redstone, getPlayerX(),getPlayerY()-1,getPlayerZ(),0,5,0,7)}
setPosition(getPlayerEnt(),x,y+3,z)
if(Aniu[16]==true){
Level.addParticle(ParticleType.redstone, getPlayerX(),getPlayerY()-2,getPlayerZ(),0,5,0,7)}}}}

function entityAddedHook(e){
if(Aniu[15]==true){
if(Entity.getEntityTypeId(e)==80){
Aniu[15]=false;setPosition
(Player.getEntity(),Entity.getX(beiod), Entity.getY(beiod)+2,Entity.getZ(beiod))}}}

function Shekuns(){
if(Aniu[15]!=true){Aniu[15]=true}
else{Aniu[15]=false}
beiod=Level.spawnMob(Player.getX()+Math.sin((getYaw()%360)*Math.PI/180)*1.5*Math.cos(getPitch()*Math.PI/180)/(-1),Player.getY()+Math.sin(getPitch()*Math.PI/180)*2.1*(-1),Player.getZ()+Math.cos((getYaw()%360)*Math.PI/180)*1.5*Math.cos(getPitch()*Math.PI/180),80)
Entity.setVelX(beiod,Math.sin((getYaw()%360)*Math.PI/180)*6*Math.cos(getPitch()*Math.PI/180)/(-1))
Entity.setVelY(beiod,Math.sin(getPitch()*Math.PI/180)*6*(-1))
Entity.setVelZ(beiod,Math.cos((getYaw()%360)*Math.PI/180)*6*Math.cos(getPitch()*Math.PI/180))}

function getPlayerent(){
return getPlayerEnt()}

function modTick(){
playerx=Math.floor(getPlayerX())
playery=Math.floor(getPlayerY())
playerz=Math.floor(getPlayerZ())
if(Aniu[1]==true){
if(Aniu[16]==true&&getTile(playerx,playery-2,playerz)!=0){
Level.addParticle(13,getPlayerX(),getPlayerY()-1,getPlayerZ(),2,2,2,5)}
if(shout==1){shout=2;xbefore=getPlayerX()
zbefore=getPlayerZ()}
if(shout==3){shout=1
xafter=getPlayerX()-xbefore
zafter=getPlayerZ()-zbefore
setVelX(getPlayerEnt(),xafter)
setVelZ(getPlayerEnt(),zafter)
xafter=0;zafter=0}
if(shout!=1){shout=shout+1}}
if(Aniu[2]==true&&Aniu[17]==true){
setVelY(getPlayerEnt(),0.7)
Aniu[17]=false}
if(Aniu[3]==true){Level.setTime(24000)}
if(Aniu[4]==true){ModPE.showTipMessage
(playerx+" "+playery+" "+playerz)}
if(Aniu[5]==true){
if(shf){shf=!shf
setPosition(getPlayerEnt(),getPlayerX()+0.001,getPlayerY()+0.01,getPlayerZ()+0.001)}
else if(!shf){shf=!shf
setPosition(getPlayerEnt(),getPlayerX()-0.001,getPlayerY()+0.01,getPlayerZ()-0.001)}}
if(Aniu[9]==true){
if(getTile(playerx,playery-2,playerz)==8||getTile(playerx,playery-2,playerz)==9){
setVelY(getPlayerEnt(),0.000000001)}}}

function dip2px(ctx,dips){
return Math.ceil(dips*ctx.getResources().getDisplayMetrics().density)}

function Miy(){
account=ModPE.readData
("Chunyulin_account")
if(account==""){
a=Math.floor(Math.random(0)*(25))
if(a==0){b="A"};if(a==1){b="B"}
if(a==2){b="C"};if(a==3){b="D"}
if(a==4){b="D"};if(a==5){b="F"}
if(a==6){b="G"};if(a==7){b="H"}
if(a==8){b="I"};if(a==9){b="J"}
if(a==10){b="K"};if(a==11){b="L"}
if(a==12){b="M"};if(a==13){b="N"}
if(a==14){b="O"};if(a==15){b="P"}
if(a==16){b="Q"};if(a==17){b="R"}
if(a==18){b="S"};if(a==19){b="T"}
if(a==20){b="U"};if(a==21){b="V"}
if(a==22){b="W"};if(a==23){b="X"}
if(a==24){b="Y"};if(a==25){b="Z"}
miy=Math.floor(Math.random
(1234567890)*(9876543210))
ModPE.saveData
("Chunyulin_account",b+miy)}}

registration=ModPE.readData
("Chunyulin_registration")
if(registration=="yes"){Chuappion()}
else{Miy();Denlu()}

function Denlu(){
account=ModPE.readData
("Chunyulin_account")
ctx.runOnUiThread(new java.lang.Runnable({run:function(){try{
var layout=new android.widget.LinearLayout(ctx)
var dialog=new android.app.Dialog(ctx)
dialog.setContentView(layout)
dialog.setTitle("服务器辅助JS 注册系统")
dialog.show()
var text=new android.widget.TextView(ctx)
text.setText(" 为了防止熊孩子恶意使用本辅助，所以您需要注册\n")
text.setTextColor
(android.graphics.Color.rgb(255,255,255))
layout.addView(text)
var text=new android.widget.TextView(ctx)
text.setTextSize(20)
text.setText("     "+account+"  (点击复制)")
text.setOnClickListener(new android.view.View.OnClickListener(){
onClick:function(v){
var cmb=ctx.getSystemService(ctx.CLIPBOARD_SERVICE);cmb.setText(account)
print("已复制到剪切板")}})
text.setTextColor
(android.graphics.Color.rgb(255,255,255))
layout.addView(text)
var edittext=new android.widget.EditText(ctx)
edittext.setHint("请输入注册码")
layout.setOrientation(1)
layout.addView(edittext)
var button=new android.widget.Button(ctx)
button.setText("注册")
button.setOnClickListener(new android.view.View.OnClickListener(){
onClick:function(v){
if(edittext.getText()=="Chunyulin"){
ModPE.removeData("Chunyulin_account")
Miy();dialog.dismiss();Denlu()}
else{a=account.substr(1,1)
b=account.substr(3,3)
c=account.substr(5,5)
d=account.substr(7,7)
if(a=="A"){a=b*2;ab=c*d+147}
else if(a=="D"){a=b*4;ab=c*d+258}
else if(a=="G"){a=b*6;ab=c*d+369}
else{a=b*8;ab=c*d-3};miyii=a+ab+c*d
if(edittext.getText()==miyii){Chuappion()
dialog.dismiss();ModPE.saveData
("Chunyulin_registration","yes")
print("恭喜您注册成功，祝您游戏愉快！")}}}})
layout.addView(button)
var text=new android.widget.TextView(ctx)
text.setText("\n   没有注册码？点击获取>>>")
text.setOnClickListener(new android.view.View.OnClickListener(){
onClick:function(v){Httpurl()}})
text.setTextColor
(android.graphics.Color.rgb(100,255,255))
layout.addView(text)
}catch(err){print("错误:"+err)}}}))}

function Chuappion(){
ctx.runOnUiThread(new java.lang.Runnable({run:function(){try{
var layout=new android.widget.LinearLayout(ctx)
var button=new android.widget.Button(ctx)
button.setBackgroundColor(android.graphics.Color.argb(0,0,0,0))
button.setText("F")
button.setOnClickListener(new android.view.View.OnClickListener(){
onClick:function(v){openMenu()}})
layout.addView(button)
openWindow=new android.widget.PopupWindow(layout,dip2px(ctx,36), dip2px(ctx,36))
openWindow.showAtLocation(ctx.getWindow().getDecorView(),android.view.Gravity.BOTTOM|android.view.Gravity.LEFT,0,0)
var layout=new android.widget.LinearLayout(ctx)
var button=new android.widget.Button(ctx)
button.setBackgroundColor(android.graphics.Color.argb(30,255,255,255))
button.setText("··")
button.setOnClickListener(new android.view.View.OnClickListener(){
onClick:function(v){Chatoptions()}})
layout.addView(button)
openWindow=new android.widget.PopupWindow(layout,dip2px(ctx,34), dip2px(ctx,32))
openWindow.showAtLocation(ctx.getWindow().getDecorView(),android.view.Gravity.TOP|android.view.Gravity.RIGHT,0,70)
}catch(err){print("错误:"+err)}}}))}

function openMenu(){
var layout=new android.widget.LinearLayout(ctx);try{
var menu=new android.widget.PopupWindow(layout,dip2px(ctx,85), dip2px(ctx,35));menu.setFocusable(true)
mainMenu=menu;layout.setOrientation(1)
var title=new android.widget.TextView(ctx)
title.setTextSize(22)
title.setTextColor(android.graphics.Color.rgb(255,255,255));
title.setText("服务器辅助")
layout.addView(title)
var title=new android.widget.TextView(ctx)
title.setTextSize(14);title.setTextColor
(android.graphics.Color.rgb(255,255,255))
title.setText
("v"+Version[0]+"."+Version[1]+"."+Version[2]+" 正式版")
layout.addView(title)
var CheckBox=new android.widget.CheckBox(ctx);CheckBox.setText("飞行模式*")
CheckBox.setChecked(Aniu[0])
CheckBox.setOnCheckedChangeListener(new android.widget.CompoundButton.OnCheckedChangeListener(){
onCheckedChanged:function(v,isChecked){
if(Aniu[0]!=true){Player.setCanFly(true)
Aniu[0]=true
clientMessage(Color+"【JS】飞行模式已开启")}
else{Player.setCanFly(false);Aniu[0]=false
clientMessage(Color+"【JS】飞行模式已关闭")}
Aniu[0]=isChecked}})
layout.addView(CheckBox)
var CheckBox=new android.widget.CheckBox(ctx);CheckBox.setText("疾跑模式")
CheckBox.setChecked(Aniu[1])
CheckBox.setOnCheckedChangeListener(new android.widget.CompoundButton.OnCheckedChangeListener(){
onCheckedChanged:function(v,isChecked){
if (Aniu[1]!=true){Aniu[1]=true
ModPE.setFov(90)
clientMessage(Color+"【JS】疾跑模式已开启")}
else{Aniu[1]=false
ModPE.setFov(70)
clientMessage(Color+"【JS】疾跑模式已关闭")}
Aniu[1]=isChecked}})
layout.addView(CheckBox)
var CheckBox=new android.widget.CheckBox(ctx);CheckBox.setText("超级跳跃")
CheckBox.setChecked(Aniu[2])
CheckBox.setOnCheckedChangeListener(new android.widget.CompoundButton.OnCheckedChangeListener(){
onCheckedChanged:function(v,isChecked){
if(Aniu[2]!=true){Chaion();Aniu[2]=true
clientMessage(Color+"【JS】超级跳跃已开启")}
else{open_tivnu.dismiss();Aniu[2]=false
clientMessage(Color+"【JS】超级跳跃已关闭")}
Aniu[2]=isChecked}})
layout.addView(CheckBox)
var CheckBox=new android.widget.CheckBox(ctx);CheckBox.setText("锁定白天")
CheckBox.setChecked(Aniu[3])
CheckBox.setOnCheckedChangeListener(new android.widget.CompoundButton.OnCheckedChangeListener(){
onCheckedChanged:function(v,isChecked){
if (Aniu[3]!=true){Aniu[3]=true
setTile(playerx,playery,playerz,1)
Block.setLightLevel(0,15)
setTile(playerx,playery,playerz,0)
clientMessage(Color+"【JS】锁定白天已开启")}
else{Aniu[3]=false
setTile(playerx,playery,playerz,1)
Block.setLightLevel(0,0)
setTile(playerx,playery,playerz,0)
clientMessage(Color+"【JS】锁定白天已关闭")}
Aniu[3]=isChecked}})
layout.addView(CheckBox)
var title=new android.widget.TextView(ctx)
title.setTextSize(eaed)
layout.addView(title)
var button=new android.widget.Button(ctx)
button.setText("玩家坐标")
button.setBackgroundColor(android.graphics.Color.argb(30,225,225,225))
button.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(viewarg){
Playsettings()}}));layout.addView(button)
var title=new android.widget.TextView(ctx)
title.setTextSize(eaed)
layout.addView(title)
var button=new android.widget.Button(ctx)
button.setText("挖矿助手")
button.setBackgroundColor(android.graphics.Color.argb(30,225,225,225))
button.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(viewarg){Kuancoob()}}))
layout.addView(button)
var title=new android.widget.TextView(ctx)
title.setTextSize(eaed)
layout.addView(title)
var button=new android.widget.Button(ctx);
button.setText("其它功能")
button.setBackgroundColor(android.graphics.Color.argb(30,225,225,225))
button.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(viewarg){Genduog()}}))
layout.addView(button)
var title=new android.widget.TextView(ctx)
title.setTextSize(eaed)
layout.addView(title)
var button=new android.widget.Button(ctx)
button.setText("辅助设定")
button.setBackgroundColor(android.graphics.Color.argb(30,225,225,225))
button.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(viewarg){Fenactment()}}))
layout.addView(button)
var title=new android.widget.TextView(ctx)
title.setTextSize(eaed)
layout.addView(title)
var button=new android.widget.Button(ctx);
button.setText("关于")
button.setBackgroundColor(android.graphics.Color.argb(30,225,225,225))
button.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(viewarg){About()}}))
layout.addView(button)
var title=new android.widget.TextView(ctx)
title.setTextSize(10);title.setText
("没有进入服务器的情况下请不要修改此页面的任何选项！");title.setTextColor
(android.graphics.Color.rgb(255,255,255))
layout.addView(title)
var mlayout=makeMenu(ctx,menu,layout)
menu.setContentView(mlayout)
menu.setWidth(ctx.getWindowManager().getDefaultDisplay().getWidth()*0.25)
menu.setHeight(ctx.getWindowManager().getDefaultDisplay().getHeight())
menu.setBackgroundDrawable(new android.graphics.drawable.ColorDrawable(android.graphics.Color.TRANSPARENT))
menu.showAtLocation(ctx.getWindow().getDecorView(),android.view.Gravity.RIGHT|android.view.Gravity.TOP,0,0)}catch(err){print("错误:"+err)}}

function makeMenu(ctx,menu,layout){
var mlayout=new android.widget.RelativeLayout(ctx)
var svParams=new android.widget.RelativeLayout.LayoutParams(android.widget.RelativeLayout.LayoutParams.FILL_PARENT,android.widget.RelativeLayout.LayoutParams.FILL_PARENT)
var scrollview=new android.widget.ScrollView(ctx);var pad=dip2px(ctx,5)
scrollview.setPadding(pad,pad,pad,pad)
scrollview.setLayoutParams(svParams)
scrollview.addView(layout)
mlayout.addView(scrollview)
return mlayout}

function XRay(){
playerx=Math.floor(getPlayerX())
playery=Math.floor(getPlayerY())
playerz=Math.floor(getPlayerZ())
blockid=getTile(playerx,playery,playerz)
if(blockid==0){
clientMessage(Color+"【JS】矿物追踪已开启")
setTile(playerx,playery,playerz,46)
setTile(playerx,playery-1,playerz,46)}
if(blockid==46){
clientMessage(Color+"【JS】矿物追踪已关闭")
setTile(playerx,playery,playerz,0)
setTile(playerx,playery-1,playerz,0)}}

function Playsettings(){
playerx=getPlayerX()
playery=getPlayerY()
playerz=getPlayerZ()
var layout=new android.widget.LinearLayout(ctx);try{
var menu=new android.widget.PopupWindow(layout,dip2px(ctx,75), dip2px(ctx,30));menu.setFocusable(true)
layout.setOrientation(1)
var title=new android.widget.TextView(ctx)
title.setTextSize(20)
title.setText("玩家坐标")
title.setTextColor
(android.graphics.Color.rgb(255,255,255))
layout.addView(title)
var title=new android.widget.TextView(ctx)
title.setTextSize(14)
title.setText("X坐标：")
title.setTextColor
(android.graphics.Color.rgb(255,255,255))
layout.addView(title)
var deian_x=new android.widget.EditText(ctx)
deian_x.setText
(String(getPlayerX(getPlayerent())))
deian_x.setInputType(android.text.InputType.TYPE_CLASS_NUMBER)
layout.addView(deian_x)
var title=new android.widget.TextView(ctx)
title.setTextSize(14)
title.setText("Y坐标：")
title.setTextColor
(android.graphics.Color.rgb(255,255,255))
layout.addView(title)
var deian_y=new android.widget.EditText(ctx);deian_y.setText
(String(getPlayerY(getPlayerent())))
deian_y.setInputType(android.text.InputType.TYPE_CLASS_NUMBER)
layout.addView(deian_y)
var title=new android.widget.TextView(ctx)
title.setTextSize(14)
title.setText("Z坐标：")
title.setTextColor
(android.graphics.Color.rgb(255,255,255))
layout.addView(title)
var deian_z=new android.widget.EditText(ctx);deian_z.setText
(String(getPlayerZ(getPlayerent())))
deian_z.setInputType(android.text.InputType.TYPE_CLASS_NUMBER)
layout.addView(deian_z)
var button=new android.widget.Button(ctx)
button.setText("定点传送")
button.setBackgroundColor(android.graphics.Color.argb(30,225,225,225))
button.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(viewarg){
setPosition(getPlayerent(),deian_x.getText(),deian_y.getText(),deian_z.getText())}}));layout.addView(button)
var title=new android.widget.TextView(ctx)
title.setTextSize(eaed)
layout.addView(title)
var button=new android.widget.Button(ctx)
button.setText("发送我的坐标")
button.setBackgroundColor(android.graphics.Color.argb(30,225,225,225))
button.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(viewarg){Server.sendChat
(playerx+","+playery+","+playerz)}}))
layout.addView(button)
var title=new android.widget.TextView(ctx)
title.setTextSize(eaed)
layout.addView(title)
var CheckBox=new android.widget.CheckBox(ctx)
CheckBox.setText("射箭传送*")
CheckBox.setChecked(Aniu[8])
CheckBox.setOnCheckedChangeListener(new android.widget.CompoundButton.OnCheckedChangeListener(){
onCheckedChanged:function(v,isChecked){
if(Aniu[8]!=true){Tiunken();Aniu[8]=true
clientMessage(Color+"【JS】射箭传送已开启")}
else{open_suvti.dismiss();Aniu[8]=false
clientMessage(Color+"【JS】射箭传送已关闭")}
Aniu[8]=isChecked}})
layout.addView(CheckBox)
var CheckBox=new android.widget.CheckBox(ctx)
CheckBox.setText("显示坐标")
CheckBox.setChecked(Aniu[4])
CheckBox.setOnCheckedChangeListener(new android.widget.CompoundButton.OnCheckedChangeListener(){
onCheckedChanged:function(v,isChecked){
if(Aniu[4]!=true){Aniu[4]=true
clientMessage(Color+"【JS】显示坐标已开启")}
else{Aniu[4]=false
ModPE.showTipMessage("")
clientMessage(Color+"【JS】显示坐标已关闭")}
Aniu[4]=isChecked}})
layout.addView(CheckBox)
var title=new android.widget.TextView(ctx)
title.setTextSize(eaed)
layout.addView(title)
var button=new android.widget.Button(ctx)
button.setText("记录所在坐标")
button.setBackgroundColor(android.graphics.Color.argb(30,255,225,225))
button.setOnClickListener(new android.view.View.OnClickListener({onClick:function(viewarg){clientMessage
(Color+"【JS】已记录当前所在位置坐标")
jilob=playerx;jilon=playery;jilot=playerz
if(Aniu[14]!=true){Aniu[14]=true}}}))
layout.addView(button)
var title=new android.widget.TextView(ctx)
title.setTextSize(eaed)
layout.addView(title)
var button=new android.widget.Button(ctx)
button.setText("传送至记录坐标")
button.setBackgroundColor(android.graphics.Color.argb(30,255,225,225))
button.setOnClickListener(new android.view.View.OnClickListener({onClick:function(viewarg){if(Aniu[14]==true){
clientMessage(Color+"【JS】你已传送至记录坐标")
setPosition
(getPlayerent(),jilob,jilon+1,jilot)}
else{clientMessage
(Color+"【JS】你没有记录坐标哦")}}}))
layout.addView(button)
var mlayout=makeMenu(ctx,menu,layout)
menu.setContentView(mlayout)
menu.setWidth(ctx.getWindowManager().getDefaultDisplay().getWidth()*0.25)
menu.setHeight(ctx.getWindowManager().getDefaultDisplay().getHeight())
menu.setBackgroundDrawable(new android.graphics.drawable.ColorDrawable(android.graphics.Color.TRANSPARENT))
menu.showAtLocation(ctx.getWindow().getDecorView(),android.view.Gravity.RIGHT|android.view.Gravity.TOP,ctx.getWindowManager().getDefaultDisplay().getWidth()*0.25,0)
}catch(err){print("错误:"+err)}}

function Chaion(){
ctx.runOnUiThread(new java.lang.Runnable({run:function(){try{
var layout=new android.widget.LinearLayout(ctx)
var button=new android.widget.Button(ctx);
button.setBackgroundColor(android.graphics.Color.argb(0,0,0,0))
button.setText("●")
button.setOnClickListener(new android.view.View.OnClickListener(){
onClick:function(v){
if(Aniu[17]==true){Aniu[17]=false
if(Aniu[16]==true){
Level.addParticle(ParticleType.cloud, getPlayerX(),getPlayerY()-1,getPlayerZ(),0,0,0,10)}}
else{Aniu[17]=true}}})
layout.addView(button)
open_tivnu=new android.widget.PopupWindow
(layout,dip2px(ctx,40),dip2px(ctx,40))
open_tivnu.showAtLocation(ctx.getWindow().getDecorView(),android.view.Gravity.BOTTOM|android.view.Gravity.RIGHT,+50,+50)
}catch(err){print("错误:"+err)}}}))}

function Tiunken(){
ctx.runOnUiThread(new java.lang.Runnable({run:function(){try{
var layout=new android.widget.
LinearLayout(ctx)
var button=new android.widget.Button(ctx)
button.setBackgroundColor(android.graphics.Color.argb(30,255,255,255))
button.setText("S")
button.setOnClickListener(new android.view.View.OnClickListener(){
onClick:function(v){Shekuns()}})
layout.addView(button)
open_suvti=new android.widget.PopupWindow
(layout,dip2px(ctx,40),dip2px(ctx,40))
open_suvti.showAtLocation(ctx.getWindow().getDecorView(),android.view.Gravity.BOTTOM|android.view.Gravity.RIGHT,+50,+160)
}catch(err){print("错误:"+err)}}}))}

function XRayken(){
ctx.runOnUiThread(new java.lang.Runnable({run:function(){try{
var layout=new android.widget.LinearLayout(ctx)
var button=new android.widget.Button(ctx)
button.setBackgroundColor(android.graphics.Color.argb(0,0,0,0))
button.setText("X-Ray")
button.setOnClickListener(new android.view.View.OnClickListener(){
onClick:function(v){XRay()}})
layout.addView(button)
open_xray=new android.widget.PopupWindow(layout,dip2px(ctx,70), dip2px(ctx,36))
open_xray.showAtLocation(ctx.getWindow().getDecorView(),android.view.Gravity.TOP|android.view.Gravity.RIGHT,70,0)
}catch(err){print("错误:"+err)}}}))}

function Genduog(){
playerx=Math.floor(getPlayerX())
playery=Math.floor(getPlayerY())
playerz=Math.floor(getPlayerZ())
var layout=new android.widget.LinearLayout(ctx);try{
var menu=new android.widget.PopupWindow(layout,dip2px(ctx,75), dip2px(ctx,30));menu.setFocusable(true)
layout.setOrientation(1)
var title=new android.widget.TextView(ctx)
title.setTextSize(20)
title.setText("其它功能")
title.setTextColor(android.graphics.Color.rgb(255,255,255))

layout.addView(title)
var CheckBox=new android.widget.CheckBox(ctx)
CheckBox.setText("木棍瞬移")
CheckBox.setChecked(Aniu[6])
CheckBox.setOnCheckedChangeListener(new android.widget.CompoundButton.OnCheckedChangeListener(){
onCheckedChanged:function(v,isChecked){
if(Aniu[6]!=true){Aniu[6]=true
clientMessage(Color+"【JS】木棍瞬移已开启")}
else{Aniu[6]=false
clientMessage(Color+"【JS】木棍瞬移已关闭")}
Aniu[6]=isChecked}})
layout.addView(CheckBox)
var CheckBox=new android.widget.CheckBox(ctx)
CheckBox.setText("木棍骑乘")
CheckBox.setChecked(Aniu[7])
CheckBox.setOnCheckedChangeListener(new android.widget.CompoundButton.OnCheckedChangeListener(){
onCheckedChanged:function(v,isChecked){
if(Aniu[7]!=true){Aniu[7]=true
clientMessage(Color+"【JS】木棍骑乘已开启")}
else{Aniu[7]=false
clientMessage(Color+"【JS】木棍骑乘已关闭")}
Aniu[7]=isChecked}})
layout.addView(CheckBox)
var CheckBox=new android.widget.CheckBox(ctx)
CheckBox.setText("自由穿墙")
CheckBox.setChecked(Aniu[5])
CheckBox.setOnCheckedChangeListener(new android.widget.CompoundButton.OnCheckedChangeListener(){
onCheckedChanged:function(v,isChecked){
if(Aniu[5]!=true){Aniu[5]=true
clientMessage(Color+"【JS】自由穿墙已开启")}
else{Aniu[5]=false
clientMessage(Color+"【JS】自由穿墙已关闭")}
Aniu[5]=isChecked}})
layout.addView(CheckBox)
var CheckBox=new android.widget.CheckBox(ctx)
CheckBox.setText("水上漂浮*")
CheckBox.setChecked(Aniu[9])
CheckBox.setOnCheckedChangeListener(new android.widget.CompoundButton.OnCheckedChangeListener(){
onCheckedChanged:function(v,isChecked){
if(Aniu[9]!=true){Aniu[9]=true
clientMessage(Color+"【JS】水上漂浮已开启")}
else{Aniu[9]=false
clientMessage(Color+"【JS】水上漂浮已关闭")}
Aniu[9]=isChecked}})
layout.addView(CheckBox)
var title=new android.widget.TextView(ctx)
title.setTextSize(eaed)
layout.addView(title)
var button=new android.widget.Button(ctx)
button.setText("游戏变速")
button.setBackgroundColor(android.graphics.Color.argb(30,225,225,225))
button.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(viewarg){Speed()}}))
layout.addView(button)
var title=new android.widget.TextView(ctx)
title.setTextSize(eaed)
layout.addView(title)
var button=new android.widget.Button(ctx)
button.setText("游戏模式")
button.setBackgroundColor(android.graphics.Color.argb(30,225,225,225))
button.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(viewarg){
if(Aniu[17]!=true){Aniu[17]=true
Level.setGameMode(1);clientMessage
(Color+"【JS】游戏模式已更换为创造模式")}
else{Aniu[17]=false
Level.setGameMode(0);clientMessage
(Color+"【JS】游戏模式已更换为生存模式")}}}))
layout.addView(button)
var title=new android.widget.TextView(ctx)
title.setTextSize(eaed)
layout.addView(title)
var button=new android.widget.Button(ctx)
button.setText("脱离覆盖")
button.setBackgroundColor(android.graphics.Color.argb(30,225,225,225))
button.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(viewarg){
for(var i=getPlayerY();getTile(getPlayerX(),getPlayerY(),getPlayerZ())!=0;i++){
setPosition(getPlayerEnt(),getPlayerX(),i+2,getPlayerZ())
clientMessage
(Color+"【JS】您已脱离方块覆盖")}}}))
layout.addView(button)
var title=new android.widget.TextView(ctx)
title.setTextSize(eaed)
layout.addView(title)
var button=new android.widget.Button(ctx)
button.setText("自杀")
button.setBackgroundColor(android.graphics.Color.argb(30,225,225,225))
button.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(viewarg){
Player.setHealth(0)}}))
layout.addView(button)
var title=new android.widget.TextView(ctx)
title.setTextSize(eaed)
layout.addView(title)
var button=new android.widget.Button(ctx)
button.setText("截图")
button.setBackgroundColor(android.graphics.Color.argb(30,225,225,225))
button.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(viewarg){
ModPE.takeScreenshot(/sdcard/)}}))
layout.addView(button)
var mlayout=makeMenu(ctx,menu,layout)
menu.setContentView(mlayout)
menu.setWidth(ctx.getWindowManager().getDefaultDisplay().getWidth()*0.25)
menu.setHeight(ctx.getWindowManager().getDefaultDisplay().getHeight())
menu.setBackgroundDrawable(new android.graphics.drawable.ColorDrawable(android.graphics.Color.TRANSPARENT))
menu.showAtLocation(ctx.getWindow().getDecorView(),android.view.Gravity.RIGHT|android.view.Gravity.TOP,ctx.getWindowManager().getDefaultDisplay().getWidth()*0.25,0)
}catch(err){print("错误:"+err)}}

function Speed(){
ctx.runOnUiThread(new java.lang.Runnable({run:function(){try{
var layout=new android.widget.LinearLayout(ctx)
layout.setOrientation(1)
var dialog=new android.app.Dialog(ctx)
dialog.setContentView(layout)
dialog.setTitle("游戏变速");dialog.show()
var text=new android.widget.TextView(ctx)
text.setText(" 通过修改游戏速度可以做到许多意想不到的效果哦！\n 当前游戏速度："+Gamespeed+" 倍")
text.setTextColor
(android.graphics.Color.rgb(255,255,255))
layout.addView(text)
var button=new android.widget.Button(ctx)
button.setText("初始(1倍速度)")
button.setOnClickListener(new android.view.View.OnClickListener(){
onClick:function(v){dialog.dismiss()
ModPE.setGameSpeed(20);Gamespeed=1
clientMessage(Color+"【JS】游戏速度设置为初始")}});layout.addView(button)
var button=new android.widget.Button(ctx)
button.setText("1.5倍速度")
button.setOnClickListener(new android.view.View.OnClickListener(){
onClick:function(v){dialog.dismiss()
ModPE.setGameSpeed(25);Gamespeed=1.5
clientMessage(Color+"【JS】游戏速度设置为1.5倍")}});layout.addView(button)
var button=new android.widget.Button(ctx)
button.setText("2倍速度")
button.setOnClickListener(new android.view.View.OnClickListener(){
onClick:function(v){dialog.dismiss()
ModPE.setGameSpeed(30);Gamespeed=2
clientMessage(Color+"【JS】游戏速度设置为2倍")}});layout.addView(button)
var button=new android.widget.Button(ctx)
button.setText("3倍速度")
button.setOnClickListener(new android.view.View.OnClickListener(){
onClick:function(v){dialog.dismiss()
ModPE.setGameSpeed(40);Gamespeed=3
clientMessage(Color+"【JS】游戏速度设置为3倍")}});layout.addView(button)
}catch(err){print("错误:"+err)}}}))}

function Fenactment(){
var layout=new android.widget.LinearLayout(ctx);try{
var menu=new android.widget.PopupWindow(layout,dip2px(ctx,75), dip2px(ctx,30));menu.setFocusable(true)
layout.setOrientation(1)
var title=new android.widget.TextView(ctx)
title.setTextSize(20)
title.setText("辅助设定")
title.setTextColor(android.graphics.Color.rgb(255,255,255))
layout.addView(title)
var CheckBox=new android.widget.CheckBox(ctx)
CheckBox.setText("粒子效果")
CheckBox.setChecked(Aniu[16])
CheckBox.setOnCheckedChangeListener(new android.widget.CompoundButton.OnCheckedChangeListener(){
onCheckedChanged:function(v,isChecked){
if(Aniu[16]!=true){Aniu[16]=true
clientMessage(Color+"【JS】粒子效果已开启")}
else{Aniu[16]=false
clientMessage(Color+"【JS】粒子效果已关闭")}
Aniu[16]=isChecked}})
layout.addView(CheckBox)
var title=new android.widget.TextView(ctx)
title.setTextSize(eaed)
layout.addView(title)
var button=new android.widget.Button(ctx)
button.setText("按钮位置")
button.setBackgroundColor(android.graphics.Color.argb(30,225,225,225))
button.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(viewarg){
print("暂未完善，敬请期待！")}}))
layout.addView(button)
var mlayout=makeMenu(ctx,menu,layout)
menu.setContentView(mlayout)
menu.setWidth(ctx.getWindowManager().getDefaultDisplay().getWidth()*0.25)
menu.setHeight(ctx.getWindowManager().getDefaultDisplay().getHeight())
menu.setBackgroundDrawable(new android.graphics.drawable.ColorDrawable(android.graphics.Color.TRANSPARENT))
menu.showAtLocation(ctx.getWindow().getDecorView(),android.view.Gravity.RIGHT|android.view.Gravity.TOP,ctx.getWindowManager().getDefaultDisplay().getWidth()*0.25,0)
}catch(err){print("错误:"+err)}}

function About(){
var layout=new android.widget.LinearLayout(ctx);try{
var menu=new android.widget.PopupWindow(layout,dip2px(ctx,75), dip2px(ctx,30));menu.setFocusable(true)
layout.setOrientation(1)
var title=new android.widget.TextView(ctx)
title.setTextSize(20)
title.setText("关于")
title.setTextColor(android.graphics.Color.rgb(255,255,255))
layout.addView(title)
var title=new android.widget.TextView(ctx)
title.setTextSize(eaed)
layout.addView(title)
var title=new android.widget.TextView(ctx)
title.setTextSize(16)
title.setText("服务器辅助.js\n\nBy Chunyulin\nQQ:1051213119\n百度贴吧:\n@暴漫金馆\n来自Box工作室\n\n特别鸣谢：\n@Liuyan205\n来自百度贴吧\n")
title.setTextColor
(android.graphics.Color.rgb(255,255,255))
layout.addView(title)
var button=new android.widget.Button(ctx)
button.setText("关注作者")
button.setBackgroundColor(android.graphics.Color.argb(30,225,225,225))
button.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(viewarg){Httpurl()}}))
layout.addView(button)
var title=new android.widget.TextView(ctx)
title.setTextSize(12)
title.setText("关注作者及时获取最新版")
title.setTextColor
(android.graphics.Color.rgb(255,255,255))
layout.addView(title)
var mlayout=makeMenu(ctx,menu,layout)
menu.setContentView(mlayout)
menu.setWidth(ctx.getWindowManager().getDefaultDisplay().getWidth()*0.25)
menu.setHeight(ctx.getWindowManager().getDefaultDisplay().getHeight())
menu.setBackgroundDrawable(new android.graphics.drawable.ColorDrawable(android.graphics.Color.TRANSPARENT))
menu.showAtLocation(ctx.getWindow().getDecorView(),android.view.Gravity.RIGHT|android.view.Gravity.TOP,ctx.getWindowManager().getDefaultDisplay().getWidth()*0.25,0)
}catch(err){print("错误:"+err)}}

function Httpurl(){
ctx.startActivity(new android.content.Intent(android.content.Intent.ACTION_VIEW,android.net.Uri.parse("http://tieba.baidu.com/home/main?un=%E6%9A%B4%E6%BC%AB%E9%87%91%E9%A6%86")))}

function Kuancoob(){
playerx=Math.floor(getPlayerX())
playery=Math.floor(getPlayerY())
playerz=Math.floor(getPlayerZ())
var layout=new android.widget.LinearLayout(ctx);try{
var menu=new android.widget.PopupWindow(layout,dip2px(ctx,75), dip2px(ctx,30));menu.setFocusable(true)
layout.setOrientation(1)
var title=new android.widget.TextView(ctx)
title.setTextSize(20)
title.setText("挖矿助手")
title.setTextColor(android.graphics.Color.rgb(255,255,255))
layout.addView(title)
var CheckBox=new android.widget.CheckBox(ctx);CheckBox.setText("草地透视")
CheckBox.setChecked(Aniu[13])
CheckBox.setOnCheckedChangeListener(new android.widget.CompoundButton.OnCheckedChangeListener(){
onCheckedChanged:function(v,isChecked){
if(Aniu[13]!=true){Aniu[13]=true
Block.setRenderLayer(2,1)
setTile(playerx,playery,playerz,1)
setTile(playerx,playery,playerz,0)
clientMessage(Color+"【JS】草地透视已开启")}
else{Aniu[13]=false
Block.setRenderLayer(2,0)
setTile(playerx,playery,playerz,1)
setTile(playerx,playery,playerz,0)
clientMessage(Color+"【JS】草地透视已关闭")
Aniu[13]=isChecked}}})
layout.addView(CheckBox)
var CheckBox=new android.widget.CheckBox(ctx)
CheckBox.setText("矿物追踪")
CheckBox.setChecked(Aniu[10])
CheckBox.setOnCheckedChangeListener(new android.widget.CompoundButton.OnCheckedChangeListener(){
onCheckedChanged:function(v,isChecked){
XRay();Aniu[10]=isChecked}})
layout.addView(CheckBox)
var CheckBox=new android.widget.CheckBox(ctx)
CheckBox.setText("快捷按钮")
CheckBox.setChecked(Aniu[11])
CheckBox.setOnCheckedChangeListener(new android.widget.CompoundButton.OnCheckedChangeListener(){
onCheckedChanged:function(v,isChecked){
if(Aniu[11]!=true){XRayken()
Aniu[11]=true}
else{open_xray.dismiss();Aniu[11]=false}
Aniu[11]=isChecked}})
layout.addView(CheckBox)
var title=new android.widget.TextView(ctx)
title.setTextSize(eaed)
layout.addView(title)
var button=new android.widget.Button(ctx)
button.setText("生成一个火把")
button.setBackgroundColor(android.graphics.Color.argb(30,225,225,225))
button.setOnClickListener(new android.view.View.OnClickListener({
onClick:function(viewarg){
setTile(playerx,playery,playerz,50)}}))
layout.addView(button)
var title=new android.widget.TextView(ctx)
title.setTextSize(eaed)
layout.addView(title)
var CheckBox=new android.widget.CheckBox(ctx);CheckBox.setText("快速挖矿")
CheckBox.setChecked(Aniu[12])
CheckBox.setOnCheckedChangeListener(new android.widget.CompoundButton.OnCheckedChangeListener(){
onCheckedChanged:function(v,isChecked){
if(Aniu[12]!=true){Aniu[12]=true
Block.setDestroyTime(1,0.1)
Block.setDestroyTime(4,0.1)
Block.setDestroyTime(14,0.1)
Block.setDestroyTime(15,0.1)
Block.setDestroyTime(16,0.1)
Block.setDestroyTime(21,0.1)
Block.setDestroyTime(56,0.1)
Block.setDestroyTime(73,0.1)
Block.setDestroyTime(129,0.1)
clientMessage(Color+"【JS】快速挖矿已开启")}
else{Aniu[12]=false
Block.setDestroyTime(1,2)
Block.setDestroyTime(4,1.9)
Block.setDestroyTime(14,3)
Block.setDestroyTime(15,3)
Block.setDestroyTime(16,3)
Block.setDestroyTime(21,3)
Block.setDestroyTime(56,3)
Block.setDestroyTime(73,3)
Block.setDestroyTime(129,3)
clientMessage(Color+"【JS】快速挖矿已关闭")}
Aniu[12]=isChecked}})
layout.addView(CheckBox)
var CheckBox=new android.widget.CheckBox(ctx);CheckBox.setText("快速挖土")
CheckBox.setChecked(Aniu[19])
CheckBox.setOnCheckedChangeListener(new android.widget.CompoundButton.OnCheckedChangeListener(){
onCheckedChanged:function(v,isChecked){
if(Aniu[19]!=true){Aniu[19]=true
Block.setDestroyTime(2,0.1)
Block.setDestroyTime(3,0.1)
clientMessage(Color+"【JS】快速挖土已开启")}
else{Aniu[19]=false
Block.setDestroyTime(2,0.6)
Block.setDestroyTime(3,0.6)
clientMessage(Color+"【JS】快速挖土已关闭")}
Aniu[19]=isChecked}})
layout.addView(CheckBox)
var CheckBox=new android.widget.CheckBox(ctx);CheckBox.setText("快速砍伐")
CheckBox.setChecked(Aniu[18])
CheckBox.setOnCheckedChangeListener(new android.widget.CompoundButton.OnCheckedChangeListener(){
onCheckedChanged:function(v,isChecked){
if(Aniu[18]!=true){Aniu[18]=true
Block.setDestroyTime(17,0.1)
Block.setDestroyTime(17.1,0.1)
Block.setDestroyTime(17.2,0.1)
Block.setDestroyTime(17.3,0.1)
Block.setDestroyTime(162,0.1)
Block.setDestroyTime(162.1,0.1)
clientMessage(Color+"【JS】快速砍伐已开启")}
else{Aniu[18]=false
Block.setDestroyTime(17,2)
Block.setDestroyTime(17.1,2)
Block.setDestroyTime(17.2,2)
Block.setDestroyTime(17.3,2)
Block.setDestroyTime(162,2)
Block.setDestroyTime(162.1,2)
clientMessage(Color+"【JS】快速砍伐已关闭")}
Aniu[18]=isChecked}})
layout.addView(CheckBox)
var title=new android.widget.TextView(ctx)
title.setTextSize(10);title.setText
("开启快速挖矿后请用稿子来挖矿\n不然你挖到的矿物都会消失\n快速挖土和快速砍伐可以手撸")
title.setTextColor
(android.graphics.Color.rgb(255,255,255))
layout.addView(title)
var mlayout=makeMenu(ctx,menu,layout)
menu.setContentView(mlayout)
menu.setWidth(ctx.getWindowManager().getDefaultDisplay().getWidth()*0.25)
menu.setHeight(ctx.getWindowManager().getDefaultDisplay().getHeight())
menu.setBackgroundDrawable(new android.graphics.drawable.ColorDrawable(android.graphics.Color.TRANSPARENT))
menu.showAtLocation(ctx.getWindow().getDecorView(),android.view.Gravity.RIGHT|android.view.Gravity.TOP,ctx.getWindowManager().getDefaultDisplay().getWidth()*0.25,0)
}catch(err){print("错误:"+err)}}

function Chatoptions(){
data_cipher=ModPE.readData
("Chunyulin_cipher")
ctx.runOnUiThread(new java.lang.Runnable({run:function(){try{
var dialog=new android.app.AlertDialog.Builder(ctx)
dialog.setItems(new java.lang.String("发送信息,快捷指令,一键登录,密码设置").split(","),new android.content.DialogInterface.OnClickListener(){onClick:function(dia,w){
if(w==0){Sendinformation()}
else if(w==1){print("暂未完善，敬请期待！")}
else if(w==2){if(data_cipher!=""){
Server.sendChat(data_cipher)}
else{Ciphersettings()}}
else if(w==3){Ciphersettings()}}})
dialog.show()
}catch(err){print("错误:"+err)}}}))}

function Sendinformation(){
ctx.runOnUiThread(new java.lang.Runnable({run:function(){try{
var layout=new android.widget.LinearLayout(ctx)
var dialog=new android.app.Dialog(ctx)
dialog.setContentView(layout)
dialog.setTitle("发送信息");dialog.show()
var edittext=new android.widget.EditText(ctx)
edittext.setHint("请输入要发送的内容")
layout.setOrientation(1)
layout.addView(edittext)
var button=new android.widget.Button(ctx)
button.setText("确定发送")
button.setOnClickListener(new android.view.View.OnClickListener(){
onClick:function(v){dialog.dismiss()
if(edittext.getText()=="removeDate"){
ModPE.removeData("Chunyulin_registration")
ModPE.removeData("Chunyulin_account")
print("已清除JS注册信息，重启游戏生效")}
var myDate=new Date()
time=myDate.getFullYear()+"."+myDate.getMonth()+"."+myDate.getDate()+"."+myDate.getHours()+":"+myDate.getMinutes()+" "
data_history=ModPE.readData
("Chunyulin_history")
if(edittext.getText()!=""){
data_history=ModPE.saveData
("Chunyulin_history",time+edittext.getText()+"\n"+data_history)
Server.sendChat(edittext.getText())}}})
layout.addView(button)
var button=new android.widget.Button(ctx)
button.setText("发言记录")
button.setOnClickListener(new android.view.View.OnClickListener(){
onClick:function(v){Historyfy()}})
layout.addView(button)
}catch(err){print("错误:"+err)}}}))}

function Historyfy(){
data_history=ModPE.readData
("Chunyulin_history")
if(data_history==""){
data_history="\n您没有发言记录\n"}
ctx.runOnUiThread(new java.lang.Runnable({run:function(){try{
var dialog=new android.app.AlertDialog.Builder(ctx)
dialog.setTitle("发言记录")
dialog.setMessage(data_history)
dialog.setPositiveButton("清空",new android.content.DialogInterface.OnClickListener(){onClick:function(dia,w){
data_history=ModPE.removeData
("Chunyulin_history")}})
dialog.setNegativeButton("返回",new android.content.DialogInterface.OnClickListener(){
onClick:function(dia,w){}})
dialog.show()
}catch(err){print("错误:"+err)}}}))}

function Ciphersettings(){ data_cipher=ModPE.readData
("Chunyulin_cipher")
ctx.runOnUiThread(new java.lang.Runnable({run:function(){try{
var layout=new android.widget.LinearLayout(ctx)
var dialog=new android.app.Dialog(ctx)
dialog.setContentView(layout)
dialog.setTitle("设置密码");dialog.show()
var edittext=new android.widget.EditText(ctx);edittext.setText(data_cipher)
edittext.setHint("请输入密码")
layout.setOrientation(1)
layout.addView(edittext)
var button=new android.widget.Button(ctx)
if(data_cipher!=""){button.setText
("修改密码")}else{button.setText("保存密码")}
button.setOnClickListener(new android.view.View.OnClickListener(){
onClick:function(v){
if(edittext.getText()!=""){ModPE.saveData
("Chunyulin_cipher",edittext.getText())
dialog.dismiss()}}})
layout.addView(button);if(data_cipher!=""){
var button=new android.widget.Button(ctx)
button.setText("清除密码")
button.setOnClickListener(new android.view.View.OnClickListener(){onClick:function(v){
ModPE.removeData("Chunyulin_cipher")
dialog.dismiss()}})
layout.addView(button)}
}catch(err){print("错误:"+err)}}}))}