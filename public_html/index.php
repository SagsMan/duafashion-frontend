<?php
// ── CONFIG & DB ───────────────────────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_USER', 'duafrqco_dua');
define('DB_PASS', 'duastore2580');
define('DB_NAME', 'duafrqco_dua');
define('POS_BASE','https://pos.duafashion.store');
define('WA_NUM',  '2348160327173');

function db(){static $c=null;if(!$c){$c=new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);$c->set_charset('utf8mb4');}return $c;}
function img_url($img){return(!empty($img))?POS_BASE.'/'.ltrim($img,'/'):'';}

// ── API HANDLERS ──────────────────────────────────────────────────────────────
$action = $_GET['action'] ?? '';

if($action==='products'){
  header('Content-Type: application/json');
  $db=$db=db();
  $cat=isset($_GET['cat'])?(int)$_GET['cat']:0;
  $q=isset($_GET['q'])?$db->real_escape_string(trim($_GET['q'])):'';
  $pg=max(1,(int)($_GET['pg']??1));
  $lim=24;$off=($pg-1)*$lim;
  $w="i.status=1";
  if($cat>0)$w.=" AND i.category_id=$cat";
  if($q!=='')$w.=" AND (i.item_name LIKE '%$q%' OR i.item_code LIKE '%$q%')";
  $total=(int)db()->query("SELECT COUNT(*) t FROM db_items i WHERE $w")->fetch_object()->t;
  $res=db()->query("SELECT i.id,i.item_name,i.item_code,i.item_image,i.final_price,i.stock,c.category_name,b.brand_name FROM db_items i LEFT JOIN db_category c ON c.id=i.category_id LEFT JOIN db_brands b ON b.id=i.brand_id WHERE $w ORDER BY i.id DESC LIMIT $lim OFFSET $off");
  $rows=[];while($r=$res->fetch_assoc()){$r['image_url']=img_url($r['item_image']);$rows[]=$r;}
  echo json_encode(['total'=>$total,'page'=>$pg,'pages'=>(int)ceil($total/$lim),'products'=>$rows]);
  exit;
}
if($action==='cats'){
  header('Content-Type: application/json');
  $res=db()->query("SELECT c.id,c.category_name,COUNT(i.id) cnt FROM db_category c INNER JOIN db_items i ON i.category_id=c.id AND i.status=1 GROUP BY c.id,c.category_name HAVING cnt>0 ORDER BY cnt DESC");
  $out=[];while($r=$res->fetch_assoc())$out[]=$r;echo json_encode($out);exit;
}

// ── STORE INFO ────────────────────────────────────────────────────────────────
$site    = db()->query("SELECT site_name,logo FROM db_sitesettings LIMIT 1")->fetch_assoc();
$company = db()->query("SELECT company_name,mobile,phone,address,company_logo FROM db_company LIMIT 1")->fetch_assoc();
$site_name = "DU'A";
$whatsapp  = preg_replace('/\D/','', $company['mobile'] ?? WA_NUM);
$logo      = !empty($site['logo']) ? POS_BASE.'/uploads/'.$site['logo'] : POS_BASE.'/theme/images/dua-logo.jpeg';
$clogo     = !empty($company['company_logo']) ? POS_BASE.'/uploads/company/'.$company['company_logo'] : $logo;
// Override with new DU'A Nigeria logo
$logo  = 'https://duafashion.store/images/dua-logo-new.jpg';
$clogo = 'https://duafashion.store/images/dua-logo-new.jpg';
$total_prods = (int)db()->query("SELECT COUNT(*) t FROM db_items WHERE status=1")->fetch_object()->t;
$total_cats  = (int)db()->query("SELECT COUNT(DISTINCT category_id) t FROM db_items WHERE status=1")->fetch_object()->t;
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head><meta charset="utf-8">

<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title><?= h($site_name) ?> — Modesty Redefined</title>
<meta name="description" content="Shop premium modest fashion, laces, shoes & fabrics at <?= h($site_name) ?> Nigeria."/>
<link rel="icon" href="<?= $logo ?>" type="image/jpeg"/>
<!-- Font Awesome 6 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
<!-- Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
<?php function h($s){return htmlspecialchars($s??'',ENT_QUOTES);} ?>
<style>
/* ── CSS VARIABLES ──────────────────────────────────────────────────────────── */
:root{
  --gold:#C9922A;--gold-lt:#E8C06A;--gold-dim:rgba(201,146,42,.15);
  --bg:#FAF7F2;--bg2:#F2EBE0;--surface:#FFFFFF;--surface2:#F7F3EE;
  --text:#1A1208;--text2:#3D2E18;--muted:#7A6652;
  --border:#E8DDD0;--shadow:0 4px 24px rgba(26,18,8,.08);
  --radius:14px;--radius-sm:8px;
  --nav-h:68px;--ann-h:38px;
}
[data-theme="dark"]{
  --bg:#0F0B06;--bg2:#1A1208;--surface:#1E1610;--surface2:#251C12;
  --text:#F5EDE0;--text2:#D4B896;--muted:#8A7260;
  --border:#332618;--shadow:0 4px 24px rgba(0,0,0,.4);
}

/* ── RESET ──────────────────────────────────────────────────────────────────── */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--text);line-height:1.6;overflow-x:hidden;transition:background .3s,color .3s}
a{text-decoration:none;color:inherit}
img{max-width:100%;display:block}
button{cursor:pointer;font-family:'Inter',sans-serif}

/* ── ANNOUNCEMENT BAR ───────────────────────────────────────────────────────── */
.ann-bar{
  height:var(--ann-h);background:var(--text2);color:rgba(255,255,255,.85);
  display:flex;align-items:center;justify-content:center;gap:24px;
  font-size:.78rem;letter-spacing:.06em;
  position:relative;z-index:1001;
}
.ann-bar a{color:var(--gold-lt);font-weight:500}
.ann-bar i{font-size:.85rem}

/* ── NAVBAR ─────────────────────────────────────────────────────────────────── */
.navbar{
  position:sticky;top:0;z-index:1000;
  height:var(--nav-h);padding:0 5%;
  display:flex;align-items:center;gap:20px;
  background:var(--surface);border-bottom:1px solid var(--border);
  box-shadow:var(--shadow);transition:background .3s,border-color .3s;
}
.nav-brand{display:flex;align-items:center;gap:10px;flex-shrink:0}
.nav-brand img{height:38px;width:38px;object-fit:contain;border-radius:50%;border:2px solid var(--gold)}
.nav-brand-name{font-family:'Cormorant Garamond',serif;font-size:1.3rem;font-weight:600;color:var(--text);white-space:nowrap}
.nav-links{display:flex;align-items:center;gap:4px;margin:0 auto}
.nav-link{
  color:var(--muted);font-size:.87rem;font-weight:500;
  padding:8px 14px;border-radius:50px;
  transition:color .2s,background .2s;white-space:nowrap;
}
.nav-link:hover,.nav-link.active{color:var(--gold);background:var(--gold-dim)}
.nav-actions{display:flex;align-items:center;gap:10px;flex-shrink:0}
.nav-search-wrap{
  display:flex;align-items:center;background:var(--surface2);
  border:1.5px solid var(--border);border-radius:50px;
  padding:0 14px;gap:8px;transition:border-color .2s,box-shadow .2s;
}
.nav-search-wrap:focus-within{border-color:var(--gold);box-shadow:0 0 0 3px var(--gold-dim)}
.nav-search-wrap input{
  border:none;outline:none;background:transparent;
  font-size:.85rem;color:var(--text);padding:9px 0;width:160px;
  font-family:'Inter',sans-serif;
}
.nav-search-wrap input::placeholder{color:var(--muted)}
.nav-search-wrap i{color:var(--muted);font-size:.85rem}
.icon-btn{
  width:38px;height:38px;border-radius:50%;border:1.5px solid var(--border);
  background:var(--surface2);color:var(--muted);
  display:flex;align-items:center;justify-content:center;
  font-size:.9rem;transition:all .2s;flex-shrink:0;
}
.icon-btn:hover{border-color:var(--gold);color:var(--gold);background:var(--gold-dim)}
.wa-btn{
  display:flex;align-items:center;gap:7px;
  background:var(--gold);color:#fff;border:none;
  border-radius:50px;padding:9px 18px;font-size:.83rem;font-weight:600;
  transition:background .2s,transform .15s;white-space:nowrap;
}
.wa-btn:hover{background:#B5811F;transform:translateY(-1px)}
.hamburger{display:none;flex-direction:column;gap:5px;padding:6px;background:none;border:none}
.hamburger span{display:block;width:22px;height:2px;background:var(--text);border-radius:2px;transition:all .3s}

/* ── MOBILE MENU ────────────────────────────────────────────────────────────── */
.mobile-menu{
  display:none;position:fixed;inset:0;z-index:2000;
  background:var(--surface);padding:24px;
  flex-direction:column;gap:8px;overflow-y:auto;
}
.mobile-menu.open{display:flex}
.mobile-menu-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
.mobile-link{
  padding:14px 16px;border-radius:var(--radius-sm);
  font-size:1.05rem;font-weight:500;color:var(--text);
  border:1px solid var(--border);
  display:flex;align-items:center;gap:12px;
}
.mobile-link:hover{background:var(--gold-dim);color:var(--gold)}
.mobile-link i{width:20px;color:var(--gold)}

/* ── HERO CAROUSEL ──────────────────────────────────────────────────────────── */
.hero{position:relative;height:88vh;min-height:520px;overflow:hidden}
.slides{display:flex;height:100%;transition:transform .7s cubic-bezier(.4,0,.2,1)}
.slide{
  flex-shrink:0;width:100%;height:100%;position:relative;
  display:flex;align-items:center;
}
.slide-img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
.slide-overlay{position:absolute;inset:0;background:linear-gradient(90deg,rgba(26,18,8,.78) 0%,rgba(26,18,8,.35) 60%,transparent 100%)}
.slide-content{
  position:relative;z-index:1;padding:0 8%;max-width:660px;
  animation:none;
}
.slide.active .slide-content{animation:fadeUp .8s ease both}
.slide-tag{
  display:inline-flex;align-items:center;gap:7px;
  background:var(--gold);color:#fff;border-radius:50px;
  padding:5px 14px;font-size:.76rem;font-weight:600;
  letter-spacing:.1em;text-transform:uppercase;margin-bottom:20px;
}
.slide-title{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(2.4rem,5.5vw,4.2rem);
  font-weight:300;color:#fff;line-height:1.12;margin-bottom:16px;
}
.slide-title strong{font-weight:600;color:var(--gold-lt)}
.slide-sub{font-size:.97rem;color:rgba(255,255,255,.72);margin-bottom:32px;max-width:440px;font-weight:300}
.slide-ctas{display:flex;gap:14px;flex-wrap:wrap}
.btn-gold{
  background:var(--gold);color:#fff;border:none;border-radius:50px;
  padding:13px 32px;font-size:.9rem;font-weight:600;
  transition:background .2s,transform .15s,box-shadow .2s;
  box-shadow:0 4px 18px rgba(201,146,42,.45);
}
.btn-gold:hover{background:#B5811F;transform:translateY(-2px)}
.btn-outline{
  background:transparent;color:#fff;
  border:1.5px solid rgba(255,255,255,.4);border-radius:50px;
  padding:13px 32px;font-size:.9rem;font-weight:500;
  transition:all .2s;display:inline-flex;align-items:center;gap:8px;
}
.btn-outline:hover{border-color:var(--gold-lt);background:rgba(201,146,42,.12)}
/* carousel controls */
.carousel-ctrl{
  position:absolute;top:50%;transform:translateY(-50%);z-index:10;
  width:46px;height:46px;border-radius:50%;border:2px solid rgba(255,255,255,.3);
  background:rgba(255,255,255,.12);backdrop-filter:blur(6px);color:#fff;
  display:flex;align-items:center;justify-content:center;font-size:1rem;
  transition:all .2s;
}
.carousel-ctrl:hover{background:var(--gold);border-color:var(--gold)}
.ctrl-prev{left:24px}.ctrl-next{right:24px}
.carousel-dots{
  position:absolute;bottom:24px;left:50%;transform:translateX(-50%);
  display:flex;gap:8px;z-index:10;
}
.dot{width:8px;height:8px;border-radius:50%;background:rgba(255,255,255,.4);border:none;transition:all .3s}
.dot.active{background:var(--gold);width:24px;border-radius:4px}

/* ── TRUST STRIP ────────────────────────────────────────────────────────────── */
.trust-strip{
  background:var(--surface);border-bottom:1px solid var(--border);
  padding:18px 5%;display:grid;
  grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;
}
.trust-item{display:flex;align-items:center;gap:12px;padding:10px 16px}
.trust-icon{
  width:42px;height:42px;border-radius:50%;background:var(--gold-dim);
  display:flex;align-items:center;justify-content:center;
  color:var(--gold);font-size:1.1rem;flex-shrink:0;
}
.trust-text strong{font-size:.88rem;font-weight:600;color:var(--text);display:block}
.trust-text span{font-size:.76rem;color:var(--muted)}

/* ── SECTION HEADER ─────────────────────────────────────────────────────────── */
.sec{padding:72px 5%}
.sec-hd{text-align:center;margin-bottom:48px}
.sec-label{font-size:.72rem;letter-spacing:.22em;text-transform:uppercase;color:var(--gold);font-weight:600;margin-bottom:10px;display:block}
.sec-title{font-family:'Cormorant Garamond',serif;font-size:clamp(1.8rem,3.5vw,2.7rem);font-weight:400;color:var(--text)}
.sec-sub{color:var(--muted);font-size:.92rem;margin-top:8px;font-weight:300}

/* ── CATEGORY CARDS ─────────────────────────────────────────────────────────── */
.cat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:16px}
.cat-card{
  background:var(--surface);border:1.5px solid var(--border);border-radius:var(--radius);
  padding:24px 16px;text-align:center;cursor:pointer;
  transition:all .25s;
}
.cat-card:hover{transform:translateY(-4px);border-color:var(--gold);box-shadow:0 12px 32px var(--gold-dim)}
.cat-icon{
  width:60px;height:60px;border-radius:50%;margin:0 auto 14px;
  display:flex;align-items:center;justify-content:center;
  font-size:1.5rem;transition:transform .3s;
}
.cat-card:hover .cat-icon{transform:scale(1.1)}
.cat-name{font-size:.9rem;font-weight:600;color:var(--text);margin-bottom:4px;text-transform:capitalize}
.cat-cnt{font-size:.75rem;color:var(--muted)}

/* ── PRODUCT GRID ───────────────────────────────────────────────────────────── */
.prod-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:22px}
.prod-card{
  background:var(--surface);border-radius:var(--radius);overflow:hidden;
  border:1px solid var(--border);
  transition:transform .3s,box-shadow .3s;
  animation:fadeUp .45s ease both;
}
.prod-card:hover{transform:translateY(-5px);box-shadow:0 18px 48px rgba(26,18,8,.12)}
[data-theme="dark"] .prod-card:hover{box-shadow:0 18px 48px rgba(0,0,0,.4)}
.prod-img{
  position:relative;width:100%;padding-top:110%;overflow:hidden;
}
.prod-img img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;transition:transform .5s}
.prod-card:hover .prod-img img{transform:scale(1.06)}
/* placeholder when no image */
.prod-placeholder{
  position:absolute;inset:0;display:flex;flex-direction:column;
  align-items:center;justify-content:center;gap:10px;
}
.prod-placeholder i{font-size:2.4rem;opacity:.6}
.prod-placeholder span{font-size:.8rem;font-weight:500;opacity:.7;text-align:center;padding:0 12px;text-transform:capitalize}
.prod-badge{
  position:absolute;top:10px;left:10px;
  border-radius:50px;padding:3px 11px;font-size:.68rem;font-weight:700;
  letter-spacing:.07em;text-transform:uppercase;
}
.badge-stock{background:var(--gold);color:#fff}
.badge-out{background:#6B5240;color:rgba(255,255,255,.8)}
.badge-new{background:#2D7D46;color:#fff;position:absolute;top:10px;right:10px}
.prod-body{padding:14px 16px 16px}
.prod-cat{font-size:.68rem;letter-spacing:.14em;text-transform:uppercase;color:var(--gold);font-weight:600;margin-bottom:5px}
.prod-name{font-family:'Cormorant Garamond',serif;font-size:1.12rem;font-weight:500;color:var(--text);line-height:1.3;margin-bottom:12px}
.prod-foot{display:flex;align-items:center;justify-content:space-between;padding-top:12px;border-top:1px solid var(--border)}
.prod-price{font-family:'Cormorant Garamond',serif;font-size:1.15rem;font-weight:600;color:var(--text)}
.enquire-btn{
  display:flex;align-items:center;gap:5px;
  background:rgba(37,211,102,.1);color:#128C7E;
  border:1px solid rgba(37,211,102,.3);border-radius:50px;
  padding:6px 13px;font-size:.78rem;font-weight:600;
  transition:background .2s;border:none;
}
.enquire-btn:hover{background:rgba(37,211,102,.22)}

/* ── FILTER BAR ─────────────────────────────────────────────────────────────── */
.filter-bar{
  display:flex;gap:8px;overflow-x:auto;padding-bottom:4px;
  scrollbar-width:none;margin-bottom:32px;flex-wrap:wrap;
}
.filter-bar::-webkit-scrollbar{display:none}
.filter-btn{
  flex-shrink:0;border:1.5px solid var(--border);background:var(--surface);
  color:var(--muted);border-radius:50px;padding:8px 18px;
  font-size:.82rem;font-weight:500;transition:all .2s;white-space:nowrap;
}
.filter-btn:hover{border-color:var(--gold);color:var(--gold)}
.filter-btn.active{background:var(--gold);border-color:var(--gold);color:#fff}

/* ── PAGINATION ─────────────────────────────────────────────────────────────── */
.pages{display:flex;justify-content:center;gap:6px;margin-top:44px;flex-wrap:wrap}
.pg-btn{
  width:38px;height:38px;border-radius:50%;border:1.5px solid var(--border);
  background:var(--surface);color:var(--muted);font-size:.85rem;
  display:flex;align-items:center;justify-content:center;transition:all .2s;
}
.pg-btn:hover{border-color:var(--gold);color:var(--gold)}
.pg-btn.active{background:var(--gold);border-color:var(--gold);color:#fff}
.pg-btn:disabled{opacity:.3;pointer-events:none}

/* ── LOADER ─────────────────────────────────────────────────────────────────── */
.loader-wrap{grid-column:1/-1;display:flex;justify-content:center;padding:56px}
.spinner{width:38px;height:38px;border:3px solid var(--border);border-top-color:var(--gold);border-radius:50%;animation:spin .7s linear infinite}
.empty-state{grid-column:1/-1;text-align:center;padding:64px 20px;color:var(--muted);font-family:'Cormorant Garamond',serif;font-size:1.4rem}

/* ── PRODUCT MODAL ──────────────────────────────────────────────────────────── */
.modal-bg{
  display:none;position:fixed;inset:0;z-index:3000;
  background:rgba(15,11,6,.65);backdrop-filter:blur(8px);
  align-items:center;justify-content:center;padding:20px;
}
.modal-bg.open{display:flex}
.modal{
  background:var(--surface);border-radius:20px;max-width:820px;width:100%;
  max-height:90vh;overflow:hidden;display:flex;
  box-shadow:0 28px 80px rgba(0,0,0,.35);animation:fadeUp .3s ease;
}
.modal-left{width:44%;flex-shrink:0;position:relative;min-height:300px}
.modal-left img{width:100%;height:100%;object-fit:cover}
.modal-left .prod-placeholder{background:linear-gradient(135deg,#2D1A0A,#5C3A1A)}
.modal-right{flex:1;padding:32px;overflow-y:auto;display:flex;flex-direction:column;gap:12px}
.modal-close{align-self:flex-end;width:34px;height:34px;border-radius:50%;border:1.5px solid var(--border);background:var(--surface2);color:var(--muted);display:flex;align-items:center;justify-content:center;font-size:.9rem;transition:all .2s;flex-shrink:0}
.modal-close:hover{border-color:var(--gold);color:var(--gold)}
.modal-cat{font-size:.7rem;letter-spacing:.18em;text-transform:uppercase;color:var(--gold);font-weight:600}
.modal-name{font-family:'Cormorant Garamond',serif;font-size:1.9rem;font-weight:400;color:var(--text);line-height:1.2}
.modal-price{font-family:'Cormorant Garamond',serif;font-size:1.75rem;font-weight:600;color:var(--text)}
.modal-badge{display:inline-block;border-radius:50px;padding:4px 14px;font-size:.75rem;font-weight:600}
.modal-badge.in{background:rgba(45,125,70,.12);color:#2D7D46;border:1px solid rgba(45,125,70,.25)}
.modal-badge.out{background:rgba(107,82,64,.12);color:#6B5240;border:1px solid rgba(107,82,64,.25)}
.modal-wa{
  margin-top:auto;display:flex;align-items:center;justify-content:center;gap:10px;
  background:#25D366;color:#fff;border:none;border-radius:50px;padding:14px;
  font-size:.93rem;font-weight:700;transition:background .2s;
}
.modal-wa:hover{background:#1ebe5d}
.modal-meta{display:flex;flex-direction:column;gap:6px;font-size:.83rem;color:var(--muted);padding:12px 0;border-top:1px solid var(--border)}
.modal-meta span{display:flex;align-items:center;gap:8px}
.modal-meta i{color:var(--gold);width:16px}

/* ── STATS STRIP ────────────────────────────────────────────────────────────── */
.stats-strip{background:var(--text2);padding:48px 5%;display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:28px;text-align:center}
.stat-v{font-family:'Cormorant Garamond',serif;font-size:2.6rem;font-weight:300;color:var(--gold-lt)}
.stat-l{font-size:.72rem;letter-spacing:.16em;text-transform:uppercase;color:rgba(255,255,255,.45);margin-top:4px}

/* ── ABOUT ──────────────────────────────────────────────────────────────────── */
.about-wrap{display:flex;align-items:center;gap:60px;flex-wrap:wrap}
.about-img-wrap{flex-shrink:0;position:relative}
.about-logo{width:180px;height:180px;border-radius:50%;object-fit:contain;border:4px solid var(--gold);box-shadow:0 0 0 14px var(--gold-dim)}
.about-badge{
  position:absolute;bottom:8px;right:-8px;
  background:var(--gold);color:#fff;border-radius:50px;
  padding:5px 14px;font-size:.72rem;font-weight:700;
  letter-spacing:.1em;text-transform:uppercase;
  box-shadow:0 4px 12px rgba(201,146,42,.4);
}
.about-text .sec-title{text-align:left;margin-bottom:12px}
.about-text p{color:var(--muted);max-width:500px;font-weight:300;line-height:1.9;font-size:.95rem}
.about-chips{margin-top:22px;display:flex;gap:12px;flex-wrap:wrap}
.chip{display:flex;align-items:center;gap:8px;background:var(--surface2);border:1.5px solid var(--border);border-radius:50px;padding:9px 18px;font-size:.82rem;color:var(--text);transition:border-color .2s}
.chip:hover{border-color:var(--gold)}
.chip i{color:var(--gold)}

/* ── FLOAT WA ───────────────────────────────────────────────────────────────── */
.float-wa{
  position:fixed;bottom:24px;right:24px;z-index:2000;
  width:54px;height:54px;border-radius:50%;background:#25D366;
  display:flex;align-items:center;justify-content:center;
  box-shadow:0 6px 20px rgba(37,211,102,.5);
  transition:transform .2s;animation:pulse 3s ease infinite;
}
.float-wa:hover{transform:scale(1.1);animation:none}
.float-wa i{font-size:1.55rem;color:#fff}
.float-tip{
  position:absolute;right:62px;background:var(--text);color:#fff;
  font-size:.77rem;padding:6px 12px;border-radius:8px;white-space:nowrap;
  opacity:0;pointer-events:none;transition:opacity .2s;
}
.float-tip::after{content:'';position:absolute;left:100%;top:50%;transform:translateY(-50%);border:5px solid transparent;border-left-color:var(--text)}
.float-wa:hover .float-tip{opacity:1}

/* ── FOOTER ─────────────────────────────────────────────────────────────────── */
footer{background:var(--bg2);border-top:1px solid var(--border);padding:56px 5% 32px}
.footer-grid{display:grid;grid-template-columns:1.5fr 1fr 1fr;gap:48px;margin-bottom:40px}
.footer-brand img{height:44px;width:44px;object-fit:contain;border-radius:50%;border:2px solid var(--gold);margin-bottom:12px}
.footer-brand-name{font-family:'Cormorant Garamond',serif;font-size:1.3rem;font-weight:600;color:var(--text);margin-bottom:8px}
.footer-desc{font-size:.84rem;color:var(--muted);line-height:1.8;max-width:280px}
.footer-socials{display:flex;gap:10px;margin-top:16px}
.social-btn{width:36px;height:36px;border-radius:50%;border:1.5px solid var(--border);display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:.88rem;transition:all .2s}
.social-btn:hover{border-color:var(--gold);color:var(--gold);background:var(--gold-dim)}
.footer-col h4{font-size:.82rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--text);margin-bottom:16px}
.footer-links{display:flex;flex-direction:column;gap:9px}
.footer-links a{font-size:.85rem;color:var(--muted);transition:color .2s;display:flex;align-items:center;gap:8px}
.footer-links a:hover{color:var(--gold)}
.footer-links i{width:14px;font-size:.8rem}
.footer-bottom{border-top:1px solid var(--border);padding-top:24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;font-size:.8rem;color:var(--muted)}
.footer-bottom a{color:var(--gold)}

/* ── ANIMATIONS ─────────────────────────────────────────────────────────────── */
@keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
@keyframes spin{to{transform:rotate(360deg)}}
@keyframes pulse{0%,100%{box-shadow:0 6px 20px rgba(37,211,102,.5)}50%{box-shadow:0 6px 28px rgba(37,211,102,.75)}}

/* ── RESPONSIVE ─────────────────────────────────────────────────────────────── */
@media(max-width:900px){
  .nav-links,.nav-search-wrap{display:none}
  .hamburger{display:flex}
  .footer-grid{grid-template-columns:1fr 1fr}
  .footer-grid>:first-child{grid-column:1/-1}
}
@media(max-width:640px){
  .ann-bar{font-size:.7rem;gap:12px;padding:0 12px}
  .hero{height:75vh;min-height:440px}
  .slide-content{padding:0 5%}
  .prod-grid{grid-template-columns:repeat(2,1fr);gap:12px}
  .prod-body{padding:11px 13px 13px}
  .modal{flex-direction:column}
  .modal-left{width:100%;height:220px;flex-shrink:0}
  .footer-grid{grid-template-columns:1fr}
  .trust-strip{grid-template-columns:1fr 1fr;gap:4px}
  .trust-item{padding:8px 10px}
  .sec{padding:52px 4%}
  .cat-grid{grid-template-columns:repeat(3,1fr)}
}
@media(max-width:380px){
  .prod-grid{grid-template-columns:1fr}
  .cat-grid{grid-template-columns:repeat(2,1fr)}
}
</style>
</head>
<body>

<!-- ANNOUNCEMENT BAR -->
<div class="ann-bar">
  <span><i class="fa-brands fa-whatsapp"></i> &nbsp;Free WhatsApp consultation on all enquiries</span>
  <a href="https://wa.me/<?= $whatsapp ?>" target="_blank" rel="noopener">+<?= $whatsapp ?></a>
</div>

<!-- NAVBAR -->
<nav class="navbar" id="navbar">
  <a href="#" class="nav-brand">
    <img src="<?= $logo ?>" alt="<?= h($site_name) ?>"/>
    <span class="nav-brand-name"><?= h($site_name) ?></span>
  </a>

  <div class="nav-links">
    <a href="#" class="nav-link active" onclick="scrollTo({top:0,behavior:'smooth'});return false"><i class="fa-solid fa-house-chimney" style="margin-right:5px"></i>Home</a>
    <a href="#shop" class="nav-link" onclick="smoothScroll('shop');return false">Shop</a>
    <a href="#categories" class="nav-link" onclick="smoothScroll('categories');return false">Categories</a>
    <a href="#about" class="nav-link" onclick="smoothScroll('about');return false">About</a>
    <a href="https://wa.me/<?= $whatsapp ?>" target="_blank" rel="noopener" class="nav-link">Contact</a>
  </div>

  <div class="nav-actions">
    <div class="nav-search-wrap">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="text" id="search-input" placeholder="Search products…" autocomplete="off"/>
    </div>
    <button class="icon-btn" id="theme-toggle" title="Toggle dark mode" onclick="toggleTheme()">
      <i class="fa-solid fa-moon" id="theme-icon"></i>
    </button>
    <a href="https://wa.me/<?= $whatsapp ?>" target="_blank" rel="noopener" class="wa-btn">
      <i class="fa-brands fa-whatsapp"></i> <span>WhatsApp</span>
    </a>
    <button class="hamburger" id="ham-btn" onclick="toggleMenu()">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>

<!-- MOBILE MENU -->
<div class="mobile-menu" id="mobile-menu">
  <div class="mobile-menu-head">
    <span style="font-family:'Cormorant Garamond',serif;font-size:1.3rem;font-weight:600"><?= h($site_name) ?></span>
    <button class="icon-btn" onclick="toggleMenu()"><i class="fa-solid fa-xmark"></i></button>
  </div>
  <div class="nav-search-wrap" style="border-radius:12px;margin-bottom:8px">
    <i class="fa-solid fa-magnifying-glass"></i>
    <input type="text" id="search-input-mob" placeholder="Search products…" autocomplete="off" style="width:100%"/>
  </div>
  <a href="#" class="mobile-link" onclick="toggleMenu();scrollTo({top:0,behavior:'smooth'});return false"><i class="fa-solid fa-house-chimney"></i>Home</a>
  <a href="#shop" class="mobile-link" onclick="toggleMenu();smoothScroll('shop');return false"><i class="fa-solid fa-store"></i>Shop</a>
  <a href="#categories" class="mobile-link" onclick="toggleMenu();smoothScroll('categories');return false"><i class="fa-solid fa-tags"></i>Categories</a>
  <a href="#about" class="mobile-link" onclick="toggleMenu();smoothScroll('about');return false"><i class="fa-solid fa-circle-info"></i>About</a>
  <a href="https://wa.me/<?= $whatsapp ?>" target="_blank" rel="noopener" class="mobile-link"><i class="fa-brands fa-whatsapp"></i>Contact on WhatsApp</a>
  <div style="margin-top:auto;display:flex;align-items:center;justify-content:space-between;padding:16px 4px">
    <span style="font-size:.82rem;color:var(--muted)">Dark Mode</span>
    <button class="icon-btn" onclick="toggleTheme()"><i class="fa-solid fa-moon" id="theme-icon-mob"></i></button>
  </div>
</div>

<!-- HERO CAROUSEL -->
<section class="hero" id="home">
  <div class="slides" id="slides">

    <!-- Slide 1 -->
    <div class="slide active">
      <img class="slide-img" src="https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=1600&q=80" alt="Fabrics"/>
      <div class="slide-overlay"></div>
      <div class="slide-content">
        <div class="slide-tag"><i class="fa-solid fa-star"></i> New Collection</div>
        <h1 class="slide-title">Exquisite <strong>Fabrics</strong><br/>& Fine Laces</h1>
        <p class="slide-sub">Discover our premium collection of Swiss lace, Atampa, Laffaya and more — curated for the modern modest woman.</p>
        <div class="slide-ctas">
          <button class="btn-gold" onclick="smoothScroll('shop')"><i class="fa-solid fa-bag-shopping"></i> &nbsp;Shop Now</button>
          <a href="#categories" class="btn-outline" onclick="smoothScroll('categories');return false"><i class="fa-solid fa-grid-2"></i> &nbsp;Browse Categories</a>
        </div>
      </div>
    </div>

    <!-- Slide 2 -->
    <div class="slide">
      <img class="slide-img" src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=1600&q=80" alt="Shoes"/>
      <div class="slide-overlay"></div>
      <div class="slide-content">
        <div class="slide-tag"><i class="fa-solid fa-fire"></i> Best Sellers</div>
        <h1 class="slide-title">Premium <strong>Footwear</strong><br/>Collection</h1>
        <p class="slide-sub">Step out in style with our wide range of quality shoes — from everyday comfort to special occasion elegance.</p>
        <div class="slide-ctas">
          <button class="btn-gold" onclick="filterAndScroll(2)"><i class="fa-solid fa-shoe-prints"></i> &nbsp;View Shoes</button>
          <a href="https://wa.me/<?= $whatsapp ?>" target="_blank" rel="noopener" class="btn-outline"><i class="fa-brands fa-whatsapp"></i> &nbsp;Enquire</a>
        </div>
      </div>
    </div>

    <!-- Slide 3 -->
    <div class="slide">
      <img class="slide-img" src="https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=1600&q=80" alt="Fashion"/>
      <div class="slide-overlay"></div>
      <div class="slide-content">
        <div class="slide-tag"><i class="fa-solid fa-gem"></i> Modest Fashion</div>
        <h1 class="slide-title">Dress with<br/><strong>Elegance</strong> &<br/>Confidence</h1>
        <p class="slide-sub">Modesty meets style. Every piece in our store is selected to make you feel beautiful, confident and covered.</p>
        <div class="slide-ctas">
          <button class="btn-gold" onclick="smoothScroll('shop')"><i class="fa-solid fa-sparkles"></i> &nbsp;Explore All</button>
          <a href="https://wa.me/<?= $whatsapp ?>" target="_blank" rel="noopener" class="btn-outline"><i class="fa-brands fa-whatsapp"></i> &nbsp;Chat with Us</a>
        </div>
      </div>
    </div>

  </div>
  <button class="carousel-ctrl ctrl-prev" onclick="slide(-1)"><i class="fa-solid fa-chevron-left"></i></button>
  <button class="carousel-ctrl ctrl-next" onclick="slide(1)"><i class="fa-solid fa-chevron-right"></i></button>
  <div class="carousel-dots" id="dots">
    <button class="dot active" onclick="goSlide(0)"></button>
    <button class="dot" onclick="goSlide(1)"></button>
    <button class="dot" onclick="goSlide(2)"></button>
  </div>
</section>

<!-- TRUST STRIP -->
<div class="trust-strip">
  <div class="trust-item">
    <div class="trust-icon"><i class="fa-brands fa-whatsapp"></i></div>
    <div class="trust-text"><strong>WhatsApp Enquiry</strong><span>Get instant responses</span></div>
  </div>
  <div class="trust-item">
    <div class="trust-icon"><i class="fa-solid fa-shield-halved"></i></div>
    <div class="trust-text"><strong>Quality Assured</strong><span>Premium selected items</span></div>
  </div>
  <div class="trust-item">
    <div class="trust-icon"><i class="fa-solid fa-tags"></i></div>
    <div class="trust-text"><strong>Best Prices</strong><span><?= $total_prods ?>+ products available</span></div>
  </div>
  <div class="trust-item">
    <div class="trust-icon"><i class="fa-solid fa-location-dot"></i></div>
    <div class="trust-text"><strong>Nigeria Based</strong><span>Serving you locally</span></div>
  </div>
</div>

<!-- CATEGORIES -->
<section class="sec" id="categories" style="background:var(--surface2)">
  <div class="sec-hd">
    <span class="sec-label">Browse by Category</span>
    <h2 class="sec-title">Shop Our Collections</h2>
    <p class="sec-sub">From premium laces to stylish footwear — find exactly what you need</p>
  </div>
  <div class="cat-grid" id="cat-grid">
    <!-- filled by JS -->
  </div>
</section>

<!-- NEW ARRIVALS -->
<section class="sec" id="new-arrivals">
  <div class="sec-hd">
    <span class="sec-label">Just Added</span>
    <h2 class="sec-title">New Arrivals</h2>
    <p class="sec-sub">The latest additions to our collection</p>
  </div>
  <div class="prod-grid" id="new-grid">
    <div class="loader-wrap"><div class="spinner"></div></div>
  </div>
</section>

<!-- FULL SHOP -->
<section class="sec" id="shop" style="background:var(--surface2)">
  <div class="sec-hd">
    <span class="sec-label">Our Collection</span>
    <h2 class="sec-title">All Products</h2>
    <p class="sec-sub">Browse, filter and enquire via WhatsApp</p>
  </div>
  <div class="filter-bar" id="filter-bar">
    <button class="filter-btn active" data-id="0">All Items</button>
  </div>
  <div class="prod-grid" id="main-grid">
    <div class="loader-wrap"><div class="spinner"></div></div>
  </div>
  <div class="pages" id="pages"></div>
</section>

<!-- STATS -->
<div class="stats-strip">
  <div><div class="stat-v"><?= $total_prods ?>+</div><div class="stat-l">Products</div></div>
  <div><div class="stat-v"><?= $total_cats ?></div><div class="stat-l">Categories</div></div>
  <div><div class="stat-v">100%</div><div class="stat-l">Modest Fashion</div></div>
  <div><div class="stat-v">NG</div><div class="stat-l">Nigeria</div></div>
</div>

<!-- ABOUT -->
<section class="sec" id="about">
  <div class="about-wrap">
    <div class="about-img-wrap">
      <img src="<?= $clogo ?>" alt="<?= h($site_name) ?>" class="about-logo"/>
      <div class="about-badge"><i class="fa-solid fa-award"></i> Trusted Store</div>
    </div>
    <div class="about-text">
      <span class="sec-label">Our Story</span>
      <h2 class="sec-title"><?= h($site_name) ?></h2>
      <p>We are dedicated to bringing you the finest in modest fashion — carefully curated laces, fabrics, footwear and traditional wear that celebrate elegance, culture, and modern style. Every item in our collection is selected with love and quality in mind for the modern Nigerian woman.</p>
      <div class="about-chips">
        <a href="https://wa.me/<?= $whatsapp ?>" target="_blank" rel="noopener" class="chip">
          <i class="fa-brands fa-whatsapp"></i> +<?= $whatsapp ?>
        </a>
        <a href="#shop" class="chip" onclick="smoothScroll('shop');return false">
          <i class="fa-solid fa-bag-shopping"></i> Browse Products
        </a>
      </div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="footer-grid">
    <div class="footer-brand">
      <img src="<?= $logo ?>" alt="<?= h($site_name) ?>"/>
      <div class="footer-brand-name"><?= h($site_name) ?></div>
      <p class="footer-desc">Your premier destination for modest fashion in Nigeria — premium laces, fabrics, footwear and traditional wear.</p>
      <div class="footer-socials">
        <a href="https://wa.me/<?= $whatsapp ?>" target="_blank" rel="noopener" class="social-btn" title="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
        <a href="https://www.instagram.com/dua.nig?igsh=azhjaXJqbDdudHc1&utm_source=qr" target="_blank" rel="noopener" class="social-btn" title="Instagram"><i class="fa-brands fa-instagram"></i></a>
        <a href="#" class="social-btn" title="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
      </div>
    </div>
    <div class="footer-col">
      <h4>Quick Links</h4>
      <div class="footer-links">
        <a href="#" onclick="scrollTo({top:0,behavior:'smooth'});return false"><i class="fa-solid fa-chevron-right"></i>Home</a>
        <a href="#shop" onclick="smoothScroll('shop');return false"><i class="fa-solid fa-chevron-right"></i>Shop All</a>
        <a href="#categories" onclick="smoothScroll('categories');return false"><i class="fa-solid fa-chevron-right"></i>Categories</a>
        <a href="#about" onclick="smoothScroll('about');return false"><i class="fa-solid fa-chevron-right"></i>About Us</a>
        <a href="https://wa.me/<?= $whatsapp ?>" target="_blank" rel="noopener"><i class="fa-solid fa-chevron-right"></i>Contact</a>
      </div>
    </div>
    <div class="footer-col">
      <h4>Contact Us</h4>
      <div class="footer-links">
        <a href="https://wa.me/<?= $whatsapp ?>" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i>+<?= $whatsapp ?></a>
        <a href="#"><i class="fa-solid fa-location-dot"></i>Nigeria</a>
       
      </div>
      <div style="margin-top:20px;padding:14px;background:var(--gold-dim);border-radius:var(--radius-sm);border:1px solid rgba(201,146,42,.25)">
        <div style="font-size:.78rem;font-weight:600;color:var(--gold);margin-bottom:6px"><i class="fa-solid fa-clock"></i> &nbsp;Business Hours</div>
        <div style="font-size:.78rem;color:var(--muted)">Mon – Sat: 8am – 7pm<br/>Sun: By appointment</div>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <span>&copy; <?= date('Y') ?> <?= h($site_name) ?>. All rights reserved.</span>
    <span>Designed &amp; Developed by <a href="https://wa.me/2348160327173" target="_blank" rel="noopener">Intellisense Vivid Technologies</a></span>
  </div>
</footer>

<!-- PRODUCT MODAL -->
<div class="modal-bg" id="modal" onclick="if(event.target===this)closeModal()">
  <div class="modal">
    <div class="modal-left" id="modal-left">
      <div class="prod-placeholder" id="modal-placeholder" style="display:none">
        <i class="fa-solid fa-shirt"></i><span></span>
      </div>
      <img id="modal-img" src="" alt="" style="display:none"/>
    </div>
    <div class="modal-right">
      <button class="modal-close" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
      <div class="modal-cat" id="modal-cat"></div>
      <h2 class="modal-name" id="modal-name"></h2>
      <div class="modal-price" id="modal-price"></div>
      <span class="modal-badge" id="modal-stock"></span>
      <div class="modal-meta" id="modal-meta"></div>
      <a href="#" target="_blank" rel="noopener" class="modal-wa" id="modal-wa">
        <i class="fa-brands fa-whatsapp" style="font-size:1.2rem"></i> Enquire on WhatsApp
      </a>
    </div>
  </div>
</div>

<!-- FLOATING WHATSAPP -->
<a href="https://wa.me/<?= $whatsapp ?>" target="_blank" rel="noopener" class="float-wa" id="fwa">
  <span class="float-tip">Chat with us!</span>
  <i class="fa-brands fa-whatsapp"></i>
</a>

<script>
const WA='<?= $whatsapp ?>';
const SITE=<?= json_encode($site_name) ?>;

// ── CATEGORY COLORS & ICONS ────────────────────────────────────────────────────
const CAT_STYLE={
  'shoes':     {bg:'linear-gradient(135deg,#8B4513,#D4713A)',icon:'fa-shoe-prints',   color:'#fff'},
  'laces':     {bg:'linear-gradient(135deg,#7B1F4A,#C2557A)',icon:'fa-scissors',      color:'#fff'},
  'atampa':    {bg:'linear-gradient(135deg,#1A5C3A,#38A169)',icon:'fa-shirt',          color:'#fff'},
  'laffaya':   {bg:'linear-gradient(135deg,#2C3E8A,#5875D4)',icon:'fa-hat-wizard',     color:'#fff'},
  'boil':      {bg:'linear-gradient(135deg,#7A5C00,#C9922A)',icon:'fa-person-dress',   color:'#fff'},
  'shirt':     {bg:'linear-gradient(135deg,#1A3A5C,#2E6DA4)',icon:'fa-shirt',          color:'#fff'},
  'default':   {bg:'linear-gradient(135deg,#3D2E18,#7A6652)',icon:'fa-bag-shopping',   color:'#fff'},
};
function catStyle(name){
  const k=(name||'').toLowerCase().trim();
  return CAT_STYLE[k]||CAT_STYLE['default'];
}
function placeholderHtml(catName,label,size='full',hidden=false){
  const s=catStyle(catName);
  const fs=size==='thumb'?'1.6rem':'2.4rem';
  return `<div class="prod-placeholder" style="${hidden?'display:none;':''}background:${s.bg}">
    <i class="fa-solid ${s.icon}" style="font-size:${fs};color:${s.color}"></i>
    <span style="color:${s.color};font-size:.78rem;font-weight:500;text-align:center;padding:0 14px">${esc(label)}</span>
  </div>`;
}

// ── DARK MODE ──────────────────────────────────────────────────────────────────
function applyTheme(t){
  document.documentElement.setAttribute('data-theme',t);
  const icon=t==='dark'?'fa-sun':'fa-moon';
  ['theme-icon','theme-icon-mob'].forEach(id=>{const el=document.getElementById(id);if(el){el.className='fa-solid '+icon;}});
  localStorage.setItem('theme',t);
}
function toggleTheme(){applyTheme(document.documentElement.getAttribute('data-theme')==='dark'?'light':'dark');}
applyTheme(localStorage.getItem('theme')||'light');

// ── MOBILE MENU ────────────────────────────────────────────────────────────────
function toggleMenu(){document.getElementById('mobile-menu').classList.toggle('open');}

// ── SMOOTH SCROLL ──────────────────────────────────────────────────────────────
function smoothScroll(id){
  const el=document.getElementById(id);
  if(el){const top=el.getBoundingClientRect().top+scrollY-70;window.scrollTo({top,behavior:'smooth'});}
}

// ── CAROUSEL ───────────────────────────────────────────────────────────────────
let curSlide=0,slideTimer;
const slides=document.querySelectorAll('.slide'),dots=document.querySelectorAll('.dot');
function goSlide(n){
  slides[curSlide].classList.remove('active');
  dots[curSlide].classList.remove('active');
  curSlide=((n%slides.length)+slides.length)%slides.length;
  document.getElementById('slides').style.transform=`translateX(-${curSlide*100}%)`;
  slides[curSlide].classList.add('active');
  dots[curSlide].classList.add('active');
  resetTimer();
}
function slide(d){goSlide(curSlide+d);}
function resetTimer(){clearInterval(slideTimer);slideTimer=setInterval(()=>slide(1),5500);}
resetTimer();

// ── CATEGORIES ─────────────────────────────────────────────────────────────────
async function loadCats(){
  const res=await fetch('?action=cats');
  const cats=await res.json();
  // Category grid
  const grid=document.getElementById('cat-grid');
  grid.innerHTML='';
  cats.forEach(c=>{
    const s=catStyle(c.category_name);
    const div=document.createElement('div');
    div.className='cat-card';div.onclick=()=>filterAndScroll(c.id);
    div.innerHTML=`<div class="cat-icon" style="background:${s.bg}"><i class="fa-solid ${s.icon}" style="color:${s.color}"></i></div>
      <div class="cat-name">${esc(c.category_name)}</div>
      <div class="cat-cnt">${c.cnt} items</div>`;
    grid.appendChild(div);
  });
  // Filter bar
  const bar=document.getElementById('filter-bar');
  cats.forEach(c=>{
    const b=document.createElement('button');
    b.className='filter-btn';b.dataset.id=c.id;
    b.textContent=c.category_name+' ('+c.cnt+')';
    b.onclick=()=>pick(c.id,b);
    bar.appendChild(b);
  });
}

// ── NEW ARRIVALS ────────────────────────────────────────────────────────────────
async function loadNew(){
  const res=await fetch('?action=products&pg=1&cat=0&q=');
  const d=await res.json();
  const g=document.getElementById('new-grid');g.innerHTML='';
  const show=d.products.slice(0,4);
  if(!show.length){g.innerHTML='<div class="empty-state">No products yet.</div>';return;}
  show.forEach((p,i)=>{g.appendChild(buildCard(p,i,true));});
}

// ── MAIN SHOP ──────────────────────────────────────────────────────────────────
let cat=0,page=1,stimer;
async function loadProducts(){
  const g=document.getElementById('main-grid');
  g.innerHTML='<div class="loader-wrap"><div class="spinner"></div></div>';
  const q=document.getElementById('search-input').value.trim();
  const res=await fetch(`?action=products&cat=${cat}&pg=${page}&q=${encodeURIComponent(q)}`);
  const d=await res.json();
  g.innerHTML='';
  if(!d.products.length){g.innerHTML='<div class="empty-state"><i class="fa-regular fa-face-sad-tear" style="display:block;font-size:2rem;margin-bottom:12px"></i>No products found.</div>';document.getElementById('pages').innerHTML='';return;}
  d.products.forEach((p,i)=>g.appendChild(buildCard(p,i,false)));
  renderPages(d.page,d.pages);
}
function pick(id,el){
  cat=id;page=1;
  document.querySelectorAll('.filter-btn').forEach(b=>b.classList.remove('active'));
  if(el)el.classList.add('active');
  loadProducts();
}
function filterAndScroll(id){
  cat=id;page=1;
  document.querySelectorAll('.filter-btn').forEach(b=>{b.classList.remove('active');if(b.dataset.id==id)b.classList.add('active');});
  smoothScroll('shop');
  loadProducts();
}
document.getElementById('search-input').addEventListener('input',()=>{
  clearTimeout(stimer);stimer=setTimeout(()=>{page=1;loadProducts();},420);
});
document.getElementById('search-input-mob')?.addEventListener('input',e=>{
  document.getElementById('search-input').value=e.target.value;
  clearTimeout(stimer);stimer=setTimeout(()=>{page=1;loadProducts();},420);
});

// ── BUILD PRODUCT CARD ─────────────────────────────────────────────────────────
function buildCard(p,i,isNew){
  const inStock=parseInt(p.stock)>0;
  const price=fmt(p.final_price);
  const card=document.createElement('div');
  card.className='prod-card';
  card.style.animationDelay=(i*.04)+'s';
  const hasImg=p.image_url&&!p.image_url.includes('no_image');
  card.innerHTML=`
    <div class="prod-img">
      ${hasImg
        ?`<img src="${p.image_url}" alt="${esc(p.item_name)}" loading="eager" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">${placeholderHtml(p.category_name,p.item_name,'thumb',true)}`
        :placeholderHtml(p.category_name,p.item_name,'thumb')
      }
      <span class="prod-badge ${inStock?'badge-stock':'badge-out'}">${inStock?'In Stock':'Out of Stock'}</span>
      ${isNew?'<span class="prod-badge badge-new"><i class="fa-solid fa-sparkles"></i> New</span>':''}
    </div>
    <div class="prod-body">
      <div class="prod-cat">${esc(p.category_name||'')}</div>
      <div class="prod-name">${esc(p.item_name)}</div>
      <div class="prod-foot">
        <span class="prod-price">${price}</span>
        <button class="enquire-btn" onclick="openModal(${JSON.stringify(p).replace(/"/g,'&quot;')})">
          <i class="fa-brands fa-whatsapp"></i> Enquire
        </button>
      </div>
    </div>`;
  return card;
}

// ── MODAL ──────────────────────────────────────────────────────────────────────
function openModal(p){
  const inStock=parseInt(p.stock)>0;
  const hasImg=p.image_url&&!p.image_url.includes('no_image');
  const img=document.getElementById('modal-img');
  const ph=document.getElementById('modal-placeholder');
  if(hasImg){
    img.src=p.image_url;img.alt=p.item_name;
    img.style.display='block';ph.style.display='none';
  } else {
    const s=catStyle(p.category_name);
    document.getElementById('modal-left').style.background=s.bg;
    ph.innerHTML=`<i class="fa-solid ${s.icon}" style="font-size:3rem;color:#fff"></i><span style="color:#fff;font-size:.95rem;font-weight:500;padding:0 20px;text-align:center">${esc(p.item_name)}</span>`;
    ph.style.display='flex';img.style.display='none';
  }
  document.getElementById('modal-cat').textContent=p.category_name||'';
  document.getElementById('modal-name').textContent=p.item_name;
  document.getElementById('modal-price').textContent=fmt(p.final_price);
  const sb=document.getElementById('modal-stock');
  sb.textContent=inStock?'✓ In Stock':'✗ Out of Stock';
  sb.className='modal-badge '+(inStock?'in':'out');
  const meta=document.getElementById('modal-meta');
  meta.innerHTML=
    (p.brand_name?`<span><i class="fa-solid fa-tag"></i> Brand: ${esc(p.brand_name)}</span>`:'')
    +(p.item_code?`<span><i class="fa-solid fa-barcode"></i> Code: ${esc(p.item_code)}</span>`:'')
    +`<span><i class="fa-solid fa-box"></i> Stock: ${parseFloat(p.stock)} units</span>`;
  const msg=encodeURIComponent(`Hello ${SITE}! I'm interested in: *${p.item_name}* (Code: ${p.item_code||'N/A'}) - ${fmt(p.final_price)}. Is it available?`);
  document.getElementById('modal-wa').href=`https://wa.me/${WA}?text=${msg}`;
  document.getElementById('modal').classList.add('open');
  document.body.style.overflow='hidden';
}
function closeModal(){
  document.getElementById('modal').classList.remove('open');
  document.body.style.overflow='';
}
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeModal();});

// ── PAGINATION ─────────────────────────────────────────────────────────────────
function renderPages(pg,pages){
  const el=document.getElementById('pages');el.innerHTML='';
  if(pages<=1)return;
  const prev=btn('&#8592;');prev.disabled=pg===1;prev.onclick=()=>{page--;loadProducts();scrollShop();};el.appendChild(prev);
  for(let i=1;i<=pages;i++){
    if(pages>7&&i>2&&i<pages-1&&Math.abs(i-pg)>2){
      if(i===3||i===pages-2){const d=document.createElement('span');d.textContent='…';d.style.cssText='padding:0 4px;color:var(--muted);line-height:38px';el.appendChild(d);}continue;
    }
    const b=btn(i);b.className='pg-btn'+(i===pg?' active':'');
    b.onclick=((_i)=>()=>{page=_i;loadProducts();scrollShop();})(i);el.appendChild(b);
  }
  const next=btn('&#8594;');next.disabled=pg===pages;next.onclick=()=>{page++;loadProducts();scrollShop();};el.appendChild(next);
}
function btn(label){const b=document.createElement('button');b.className='pg-btn';b.innerHTML=label;return b;}
function scrollShop(){smoothScroll('shop');}

// ── HELPERS ────────────────────────────────────────────────────────────────────
function fmt(n){return parseFloat(n).toLocaleString('en-NG',{style:'currency',currency:'NGN',minimumFractionDigits:0});}
function esc(s){const d=document.createElement('div');d.textContent=s||'';return d.innerHTML;}

// ── ACTIVE NAV ON SCROLL ───────────────────────────────────────────────────────
const sections=['home','categories','new-arrivals','shop','about'];
const navLinks=document.querySelectorAll('.nav-link');
window.addEventListener('scroll',()=>{
  let cur='home';
  sections.forEach(id=>{const el=document.getElementById(id);if(el&&el.getBoundingClientRect().top<120)cur=id;});
  navLinks.forEach(l=>{
    l.classList.toggle('active',l.getAttribute('href')==='#'+cur||(cur==='home'&&l.getAttribute('href')==='#'));
  });
});

// ── INIT ───────────────────────────────────────────────────────────────────────
loadCats();
loadNew();
loadProducts();
</script>
</body>
</html>