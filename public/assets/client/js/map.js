                           
    document.addEventListener('DOMContentLoaded', function () {
    mapboxgl.accessToken = 'pk.eyJ1IjoiYW5oZHVjYXRpMjExIiwiYSI6ImNseGEydmgzcDJ0bTEyaXB2a2pheGNrancifQ.W-edYExBgZtAUryD1hzwFg';
    var map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [105.826253, 21.007068], 
        zoom: 16,
        scrollZoom: false, // Ngăn chặn thu phóng bằng cách scroll
        // dragPan: false // Ngăn chặn dịch bản đồ
    });

    var points = [
        {
            coords: [105.825193, 21.007359],
            name: "Hội trường T45",
            image: "https://upload.wikimedia.org/wikipedia/commons/thumb/2/2d/H%E1%BB%99i_tr%C6%B0%E1%BB%9Dng_T45%2C_%C4%90%E1%BA%A1i_h%E1%BB%8Dc_Thu%E1%BB%B7_L%E1%BB%A3i%2C_H%C3%A0_N%E1%BB%99i_001.JPG/1200px-H%E1%BB%99i_tr%C6%B0%E1%BB%9Dng_T45%2C_%C4%90%E1%BA%A1i_h%E1%BB%8Dc_Thu%E1%BB%B7_L%E1%BB%A3i%2C_H%C3%A0_N%E1%BB%99i_001.JPG?20170417021012"
        },
        {
            coords: [105.824691, 21.007399],
            name: "Tòa nhà A1",
            image: "https://imgcdn.tapchicongthuong.vn/tcct-media/24/4/9/truong-dai-hoc-thuy-loi-cong-bo-phuong-an-tuyen-sinh-dai-hoc-chinh-quy-nam-2024_6614ce6b6344f.jpg"
        },
        {
            coords: [105.826021, 21.006768],
            name: "Ký túc xá K1",
            images: [
                "https://navigates.vn/wp-content/uploads/2023/05/ky-tuc-xa-dai-hoc-thuy-loi-5.jpg",
                "https://suckhoedoisong.qltns.mediacdn.vn/324455921873985536/2021/12/14/ktx-dh-thuy-loi-9-1639474389613885076469-16394744178351496798698.jpeg"
            ]
        },
        {
            coords: [105.827123, 21.006307],
            name: "Sân bóng thủy lợi",
            images: [
                "https://navigates.vn/wp-content/uploads/2023/05/ky-tuc-xa-dai-hoc-thuy-loi-5.jpg",
                "https://scontent.fhan3-2.fna.fbcdn.net/v/t1.6435-9/110322884_2774967409444232_2878289210938487471_n.jpg?_nc_cat=100&ccb=1-7&_nc_sid=5f2048&_nc_ohc=ZHZZ2cPLl6gQ7kNvgF4ofsU&_nc_ht=scontent.fhan3-2.fna&oh=00_AYCo_uTz7jrLrf0NavJT91lFWyfDYwxSNnS_R7lZdaW0dg&oe=6687E499"
            ],
            url:"http://127.0.0.1:8000/bai-viet/san-bong-ai-hoc-thuy-loi.html"
        },
        {
            coords: [105.825591, 21.007179],
            name: "Thư Viện",
            images: [
                "https://lib.tlu.edu.vn/Portals/0/images/bnr.jpg",
                "https://scontent.fhan3-1.fna.fbcdn.net/v/t39.30808-6/428457081_874788721111432_3339870399312727256_n.jpg?_nc_cat=105&ccb=1-7&_nc_sid=5f2048&_nc_ohc=0uzuxNjqqJwQ7kNvgH5Qdm1&_nc_ht=scontent.fhan3-1.fna&oh=00_AYAKKNYdMOFYWcBp_zYgqIRTajulupWxwkNdzmdcF0rSnA&oe=66699822",
                "https://scontent.fhan3-5.fna.fbcdn.net/v/t39.30808-6/428433236_874788607778110_3065895237418299555_n.jpg?_nc_cat=104&ccb=1-7&_nc_sid=5f2048&_nc_ohc=5wQxDQFdoygQ7kNvgHFiOkZ&_nc_ht=scontent.fhan3-5.fna&oh=00_AYCzTJJeehRVFoJXoMjYf-yhNr1ZLB_hUzdJl3v7jcKxkg&oe=666971C1",
                "https://lib.tlu.edu.vn/Portals/0/images/xep-sach-3.jpg"
            ]
        },
        {
            coords: [105.824716, 21.007950],
            name: "Tòa nhà A4",
            image: "https://upload.wikimedia.org/wikipedia/commons/thumb/b/b6/Nh%C3%A0_A4%2C_%C4%90%E1%BA%A1i_h%E1%BB%8Dc_Thu%E1%BB%B7_L%E1%BB%A3i%2C_H%C3%A0_N%E1%BB%99i_001.JPG/1200px-Nh%C3%A0_A4%2C_%C4%90%E1%BA%A1i_h%E1%BB%8Dc_Thu%E1%BB%B7_L%E1%BB%A3i%2C_H%C3%A0_N%E1%BB%99i_001.JPG?20170417021216"
        },
        {
            coords: [105.824362, 21.007008],
            name: "Tòa nhà A2",
            image: "https://upload.wikimedia.org/wikipedia/commons/thumb/2/21/Nh%C3%A0_A2%2C_%C4%90%E1%BA%A1i_h%E1%BB%8Dc_Thu%E1%BB%B7_L%E1%BB%A3i%2C_H%C3%A0_N%E1%BB%99i_001.JPG/1200px-Nh%C3%A0_A2%2C_%C4%90%E1%BA%A1i_h%E1%BB%8Dc_Thu%E1%BB%B7_L%E1%BB%A3i%2C_H%C3%A0_N%E1%BB%99i_001.JPG?20170417021207"
        },
        {
            coords: [105.827273, 21.005376],
            name: "Bể bơi Đại học Thủy Lợi",
            images: [
                "https://navigates.vn/wp-content/uploads/2023/05/co-so-vat-chat-dai-hoc-thuy-loi-6.jpg",
                "https://navigates.vn/wp-content/uploads/2023/05/co-so-vat-chat-dai-hoc-thuy-loi-7.jpg"
            ],
             url: "http://127.0.0.1:8000/bai-viet/test-khong-co-search.html"
        }
    ];

    points.forEach(function(point) {
        var marker = new mapboxgl.Marker()
            .setLngLat(point.coords)
            .addTo(map);

        var popupHtml = `<b>${point.name}</b><br>`;

        if (point.images) {
            var carouselId = 'carousel-' + point.name.replace(/\s+/g, '-');
            var carouselInnerHtml = point.images.map((img, index) => `
                <div class="carousel-item ${index === 0 ? 'active' : ''}">
                    <img src="${img}" class="d-block w-300" alt="${point.name}" style="width:300px;">
                </div>
            `).join('');

            popupHtml += `
                <div id="${carouselId}" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        ${carouselInnerHtml}
                    </div>
                    <a class="carousel-control-prev" href="#${carouselId}" role="button" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#${carouselId}" role="button" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            `;
        } else {
            popupHtml += `<img src="${point.image}" width='300px'>`;
        }

        var popup = new mapboxgl.Popup().setHTML(popupHtml);
        marker.setPopup(popup);

        if (point.images) {
            marker.getElement().addEventListener('click', function() {
                setTimeout(function() {
                    var carouselElement = document.getElementById(carouselId);
                    var carousel = new bootstrap.Carousel(carouselElement, {
                        interval: 3000
                    });
                    carousel.cycle();

                    var images = carouselElement.getElementsByClassName('carousel-item');
                    for (var img of images) {
                        img.addEventListener('dblclick', function() {
                            window.location.href = point.url;
                        });
                    }
                }, 100);
            });
        } else {
            var imgElement = document.querySelector(`[src="${point.image}"]`);
            if (imgElement) {
                imgElement.addEventListener('dblclick', function() {
                    window.location.href = point.url;
                });
            }
        }
    });
});

                          